<?php
//====================================// Session
session_name("mzAPI");
session_start();
//====================================// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set('UTC');
http_response_code(200);
//====================================// require
require_once("classes/mzAPI.php");
//====================================// Errors
ini_set("error_log", mzAPI::ERRORS_FILE);
//====================================// classes
// classes
require_once("classes/mzDatabase.php");
require_once("classes/mzParams.php");
//====================================// run
mzAPI::run();
