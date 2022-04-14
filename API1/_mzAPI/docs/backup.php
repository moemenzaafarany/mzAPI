<?php
//====================================================================================================
$date = date('Y-m-d');
$DATA = [];
//
foreach (mzAPI::$DATABASES as $mzDatabase) {
    //====================================================================================================
    //database
    $r = $mzDatabase->connect();
    if ($r->status != 200) mzAPI::response($r->status, $r->error, $r->message, $r->data);
    //====================================================================================================
    $r = $mzDatabase->listTables();
    if ($r->status != 200)  mzAPI::response($r->status, $r->error, $r->message, $r->data);
    $ts = $r->data;
    //====================================================================================================
    $tables = [];
    $data = [];
    foreach ($ts as $table) {
        $r = $mzDatabase->listColumns($table);
        if ($r->status != 200) mzAPI::response($r->status, $r->error, $r->message, $r->data);
        $tables[$table] = $r->data;
        //
        $r = $mzDatabase->select($table, "*", null, "1");
        if ($r->status != 200)  mzAPI::response($r->status, $r->error, $r->message, $r->data);
        $data[$table] = $r->data;
    }
    //====================================================================================================
    $r = $mzDatabase->listKeys();
    if ($r->status != 200)  mzAPI::response($r->status, $r->error, $r->message, $r->data);
    $keys = $r->data;
    //====================================================================================================
    $DATA[$k] = [
        "type" => $mzDatabase->database_type,
        "host" => $mzDatabase->database_host,
        "name" => $mzDatabase->database_name,
        "user" => $mzDatabase->database_user,
        "pass" => $mzDatabase->database_pass,
        "data" => [
            "keys" => $keys,
            "tables" => $tables,
            "data" => $data,
        ]
    ];
    //====================================================================================================
}
//====================================================================================================
header("Content-disposition: attachment; filename=mzAPI-UB-{mzAPI::$PROJECT}-{$date}.json");
header("Content-type: application/json");
//
echo json_encode([
    "date" => $date,
    "mzAPI-VERSION" => mzAPI::VERSION,
    "databases" => $DATA,
], JSON_UNESCAPED_UNICODE);
