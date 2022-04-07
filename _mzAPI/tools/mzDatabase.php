<?php
/* 1.0.0 */
class mzDatabase
{
    //===============================================================================//
    public $supported_types = ['mysql'];
    ///
    public $database_type;
    public $database_host;
    public $database_name;
    public $database_user;
    public $database_pass;
    public $timezone;
    ///
    public $conn = null;
    ///
    public $tables = [];
    //===============================================================================//
    public function __construct(String $database_type, String $database_host, String $database_name, String $database_user, String $database_pass, Int $timezoneInMinutes = null)
    {
        if (!in_array($database_type, $this->supported_types)) return new Exception('unsupported_database_type');
        $this->database_type = $database_type;
        $this->database_host = $database_host;
        $this->database_name = $database_name;
        $this->database_user = $database_user;
        $this->database_pass = $database_pass;
        // timezone
        if (is_numeric($timezoneInMinutes)) {
            $s = ($timezoneInMinutes - $timezoneInMinutes > $timezoneInMinutes ? "-" : "+");
            $h = floor(abs($timezoneInMinutes) / 60);
            $m = abs($timezoneInMinutes) - ($h * 60);
            $this->timezone = $s . sprintf("%02d", $h) . ":" . sprintf("%02d", $m);
        } else $this->timezone = "+00:00";
    }
    //===============================================================================//
    //===============================================================================//
    public function connect(): object
    { //array(status, results);
        try {
            $this->conn = new PDO("{$this->database_type}:host={$this->database_host};dbname={$this->database_name}", $this->database_user, $this->database_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
            $this->conn->exec("SET names utf8");
            $this->conn->exec("SET time_zone='{$this->timezone}';");
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "connection_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function beginTransaction(): object
    { //array(status, results);
        try {
            $this->conn->beginTransaction();
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "connection_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function endTransaction(bool $rollback = false): object
    { //array(status, results);
        try {
            if ($rollback == true) $this->conn->rollBack();
            else $this->conn->commit();
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "connection_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function lastInsertId(): object
    { //array(status, results);
        try {
            return mzAPI::return(200, null, null, $this->conn->lastInsertId());
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    //===============================================================================//
    public function listTables(): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("SHOW TABLES;");
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetchall(PDO::FETCH_COLUMN)); // Fetch as array
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function listKeys(): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("SELECT * FROM information_schema.key_column_usage WHERE 1;"); //CONSTRAINT_SCHEMA='{$this->database_name}';");
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetchall(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function listColumns(String $table): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("DESCRIBE {$table};");
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetchall(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function selectVersion(): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("SELECT VERSION() as version;");
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function selectTimestamp(): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("SELECT NOW() as timestamp;");
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetch(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function select(String $table, String $columns = null, String $join = null, String $arguments = "0", array $argument_bindings = null): object
    { //array(status, results);
        try {
            if (empty($columns)) $columns = "*";
            if (empty($join)) $join = "";
            ///
            $stmt = $this->conn->prepare("SELECT {$columns} FROM {$table} {$join} WHERE {$arguments};");
            ///
            if (!empty($argument_bindings)) {
                foreach ($argument_bindings as $i => $v) $stmt->bindValue($i + 1, $v);
            }
            ///
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt->fetchall(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function insert(String $table, array $data): object
    { //array(status, results);
        try {
            $columns = [];
            $values = [];
            foreach ($data as $k => $v) {
                array_push($columns, $k);
                array_push($values, "?");
            }
            $columns = implode(",", $columns);
            $values = implode(",", $values);
            ///
            $stmt = $this->conn->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$values});");
            ///
            $param = 1;
            foreach ($data as $k => $v) {
                $stmt->bindValue($param, $v);
                $param++;
            }
            ///
            $stmt->execute();
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function update(String $table, array $data, String $arguments = "0", array $argument_bindings = null): object
    { //array(status, results);
        try {
            if (empty($data)) return mzAPI::return(200);
            ///
            $columns = [];
            ///
            foreach ($data as $k => $v) {
                if (!is_int($k)) array_push($columns, "$k=?");
                else array_push($columns, $v);
            }
            $columns = implode(",", $columns);
            ///
            $stmt = $this->conn->prepare("UPDATE {$table} SET {$columns} WHERE {$arguments};");
            ///
            $param = 1;
            foreach ($data as $k => $v) {
                if (!is_int($k)) {
                    $stmt->bindValue($param, $v);
                    $param++;
                }
            }
            ///
            if (!empty($argument_bindings)) {
                foreach ($argument_bindings as $i => $v) {
                    $stmt->bindValue($param, $v);
                    $param++;
                }
            }
            ///
            $stmt->execute();
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function delete(String $table, String $arguments = "0", array $argument_bindings = null): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare("DELETE FROM {$table} WHERE {$arguments};");
            ///
            if (!empty($argument_bindings)) {
                foreach ($argument_bindings as $i => $v) $stmt->bindValue($i + 1, $v);
            }
            ///
            $stmt->execute();
            return mzAPI::return(200);
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    public function execute(String $query): object
    { //array(status, results);
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return mzAPI::return(200, null, null, $stmt);
        } catch (PDOException $e) {
            return mzAPI::return(500, "query_failed={$e->getMessage()}");
        }
    }
    //===============================================================================//
    //===============================================================================//
}
