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
//====================================// Require
require_once("classes/mzAPI.php");
require_once("classes/mzRes.php");
//====================================// mzAPI
mzAPI::init();
mzAPI::run();