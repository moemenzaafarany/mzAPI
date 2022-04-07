<?php
//====================================// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set('UTC');
http_response_code(200);
//====================================// Session
session_name("mzAPI");
session_start();
//====================================// Tools
// tools
require_once("tools/mzDatabase.php");
require_once("tools/mzParams.php");
//====================================// mzAPI Class
class mzAPI
{
    //====================================// Constants
    public const VERSION = "1";
    public const GETKEY = "mzAPIURL";
    public const STATUSES = [200 => "OK", 400 => "Bad Request", 401 => "Unauthorized", 403 => "Forbidden", 404 => "Not Found", 500 => "Internal Server Error"];
    //
    public const PHPINI = "../php.ini";
    public const CONFIGS = "../configs.php";
    public const HANDLERS = "../handlers/";
    public const INCLUDES = "../includes/";
    //====================================// Parameters
    static public $DEBUG = false;
    //
    static public $PROJECT = "";
    static public $DATABASES = [];
    static public $FOLDERS = [];
    //====================================//
    //====================================// URL
    static function URL(bool $file = false, bool $params = false): string
    { // url
        $URL = @$_GET[mzAPI::GETKEY];
        if (!empty($URL) && $file == true) $URL .= ".php";
        if ($params == true) {
            $PARAM = "";
            foreach ($_GET as $k => $v) {
                if ($k != mzAPI::GETKEY) {
                    if (is_array($v)) {
                        foreach ($v as $i => $vv) $PARAM .= "$k=$vv&";
                    } else $PARAM .= "$k=$v&";
                }
            }
            if (!empty($PARAM)) $URL .= "?" . rtrim($PARAM, "&");
        }
        return $URL;
    }

    //====================================// SETTINGS
    //====================================// Settings
    static function SETTINGS(string $PROJECT_NAME = null, bool $DEBUG_MODE = false): void
    {
        mzAPI::$PROJECT = $PROJECT_NAME;
        mzAPI::$DEBUG = $DEBUG_MODE;
    }

    //====================================// Databases
    static function DATABASE(string $name, object $class = null, array $tables = null): mzDatabase
    {
        if (!empty($name) && !empty($class)) {
            mzAPI::$DATABASES[$name] = $class;
            mzAPI::$DATABASES[$name]->tables = $tables;
        }
        if (!empty($name)) return mzAPI::$DATABASES[$name];
    }

    //====================================// Folders
    static function FOLDERS(string $name = null, string $path = null, bool $create = false): string
    {
        if (!empty($name) && !empty($path)) {
            mzAPI::$FOLDERS[$name] = "../" . $path;
            if (!is_dir(mzAPI::$FOLDERS[$name])) {
                if ($create == true && !mkdir(mzAPI::$FOLDERS[$name], 0777, true)) mzAPI::response(500, null, "mzAPI-FOLDERS-unable_to_create=={$name} '{$path}'");
                if ($create == false)  mzAPI::response(500, null, "mzAPI-FOLDERS-folder_not_exist={$name} '{$path}'");
            }
        }
        if (!empty($name)) return mzAPI::$FOLDERS[$name];
    }

    //====================================// RESPONSES
    //====================================// Return
    static function return(int $status = null, string $error = null, string $message = null, $data = null): object
    { // for funcitons
        return (object) ["status" => $status, "error" => $error, "message" => $message, "data" => $data];
    }

    //====================================// Response
    static function response(int $status = null, string $error = null, string $message = null, $data = null, $x = null): void
    {
        $response = [];
        $response["status"] = $status;
        $response["statusText"] = mzAPI::STATUSES[$status];
        $response["error"] = $error;
        $response["message"] = $message;
        $response["data"] = $data;
        if (mzAPI::$DEBUG == true) {
            $response = [
                "DEBUG" => [
                    "URL" => mzAPI::URL(true, true),
                    "DATA" => [
                        "STATUSES" => mzAPI::STATUSES,
                        "FOLDERS" => mzAPI::$FOLDERS,
                    ],
                    "ANALYTICS" => [
                        "MEMORY" => [
                            "LIMIT" => rtrim(ini_get('memory_limit'), "M") . "MB",
                            "CODE" => [
                                "PEAK" => number_format(memory_get_peak_usage(false) / 1000, 2, ".", "") . "KB",
                                "END" => number_format(memory_get_usage(false) / 1000, 2, ".", "") . "KB",
                            ],
                        ],
                        "RUNTIME" => [
                            "LIMIT" => ini_get('max_execution_time') . "S",
                            "CODE" => number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2, ".", "") . "MS",
                        ],
                    ],
                ],
            ] + $response;
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit(gc_collect_cycles());
    }

    //====================================// HANDLERS
    //====================================// tools
    static function tools(array $tools = null)
    {
        foreach ($tools as $i => $tool) {
            $file = "tools/$tool.php";
            if (is_file($file)) include_once($file);
        }
    }
    //====================================// Include
    static function include(array $includes = null)
    {
        foreach ($includes as $i => $include) {
            $file = mzAPI::INCLUDES . "/$include.php";
            if (is_file($file)) include_once($file);
        }
    }

    //====================================// API
    //====================================// RUN
    static function run(): void
    {
        //========================================// PHP INI
        if (!is_file(mzAPI::PHPINI)) {
            fopen(mzAPI::PHPINI, "w");
            file_put_contents(mzAPI::PHPINI, "# mzAPI Generated, Edit as per needed.\n\nallow_url_fopen = On\ndisplay_errors = Off\nfile_uploads = On\nmax_execution_time = 30\nmax_input_time = 60\nmax_input_vars = 100\nmax_file_uploads = 20\nmemory_limit = 256M\npost_max_size = 1000M\nupload_max_filesize = 1000M\n");
        }
        //========================================// Configs
        if (!is_file(mzAPI::CONFIGS)) {
            fopen(mzAPI::CONFIGS, "w");
            file_put_contents(mzAPI::CONFIGS, "<?php\n// Settings\nmzAPI::SETTINGS('Project Name', true);\n\n// Folders\nmzAPI::FOLDERS('folder name', 'folder path', false);\n\n// Databases\nmzAPI::DATABASE(\n\t'main',\n\tnew mzDatabase('type', 'host', 'database', 'username', 'password', 'timezone in minutes'), \n\t[\n\t't1' => 'table1'\n\t]\n);\n");
        }
        include_once(mzAPI::CONFIGS);
        //========================================// Handlers
        if (!is_dir(mzAPI::HANDLERS)) {
            if (mkdir(mzAPI::HANDLERS, 0777, true)) {
                fopen(mzAPI::HANDLERS . "example.php", "w");
                file_put_contents(mzAPI::HANDLERS . "example.php", "<?php\nprint('hello');");
            }
        }
        //========================================// Includes
        if (!is_dir(mzAPI::INCLUDES)) {
            if (mkdir(mzAPI::INCLUDES, 0777, true)) {
                fopen(mzAPI::INCLUDES . "example.php", "w");
                file_put_contents(mzAPI::INCLUDES . "example.php", "<?php\nfunction myPrint() {\n\tprint('hello');\n}");
            }
        }
        //========================================// Run
        switch (mzAPI::URL(false, true)) {
            case "?utilities=backup":
                include_once("utilities/backup.php");
                break;
            case "?utilities=documentation":
                include_once("utilities/documentation/index.php");
                break;
            default:
                try {
                    $handler = mzAPI::HANDLERS . mzAPI::URL(true);
                    if (is_file($handler)) include_once($handler);
                    else mzAPI::response(404, "url_not_found");
                } catch (Exception $e) {
                    mzAPI::response(500, $e);
                }
        }
        exit();
    }
}
//====================================//
mzAPI::run();
//====================================//