<?php
/* 1.0.0 */
class MZFtp
{
    //===============================================================================//
    public $ftp_host;
    public $ftp_port;
    public $ftp_user;
    public $ftp_pass;
    public $ftps;
    public $timeout;
    ///
    public $conn = null;
    ///
    public $tables = [];
    //===============================================================================//
    public function __construct(String $ftp_host, Int $ftp_port = 21, String $ftp_user, String $ftp_pass, bool $ftps = false, Int $timeout = 90)
    {
        $this->ftp_host = $ftp_host;
        $this->ftp_port = $ftp_port;
        $this->ftp_user = $ftp_user;
        $this->ftp_pass = $ftp_pass;
        $this->ftps = $ftps;
        $this->timeout = $timeout;
    }
    //===============================================================================//
    //===============================================================================//
    public function connect(): mzRes
    { //array(status, results);
        try {
            if ($this->ftps) $this->conn = ftp_ssl_connect($this->ftp_host, $this->ftp_port, $this->timeout);
            else $this->conn = ftp_connect($this->ftp_host, $this->ftp_port, $this->timeout);
            @ftp_login($this->conn, $this->ftp_user, $this->ftp_pass);
        } catch (Exception $e) {
            return new mzRes(500, "connection_failed={$e}");
        }
    }
    //===============================================================================//
}
