<?php
/* 1.0.0 */
class mzAPI
{
    //====================================// variables
    public const VERSION = "1";
    public const STATUSES = [200 => "OK", 400 => "Bad Request", 401 => "Unauthorized", 403 => "Forbidden", 404 => "Not Found", 429 => "Too Many Requests", 500 => "Internal Server Error"];
    //
    static public string $TITLE = "new Project";
    static public string $VERSION = "1";
    static public bool $DEBUG = true;
    //
    static public array $DBS = [];
    //====================================// HTACCESS
    public const HTACCESS_FILE = "../.htaccess";
    public const HTACCESS_CONTENT = "# mzAPI generated DO NOT EDIT\n<IfModule mod_rewrite.c>\n\tOptions -Multiviews\n\tRewriteEngine on\n\tRewriteRule ^(.*)$ _mzAPI/index.php?mzAPIURL=$1 [QSA,END]\n</IfModule>\n# mzAPI generated EDIT AS PER NEEDED\n# to add a new value just put:\n# php_flag <key> <on/off>\n# php_value <key> <value>\nphp_flag log_errors on\nphp_flag display_errors off\nphp_flag display_startup_errors off\nphp_flag ignore_repeated_errors off\nphp_flag ignore_repeated_source off\nphp_flag report_memleaks on\nphp_flag html_errors on\nphp_flag xmlrpc_errors\toff\nphp_flag xmlrpc_error_number off\nphp_flag allow_url_fopen on\nphp_flag allow_url_include on\nphp_flag file_uploads on\nphp_value error_reporting -1\nphp_value log_errors_max_len 1024\nphp_value error_log '../errors.log'\n# inputs and memory\nphp_value max_execution_time 30\nphp_value memory_limit 256M\nphp_value max_input_time 60\nphp_value max_input_vars 100000\nphp_value max_file_uploads 100000\nphp_value post_max_size 1000M\nphp_value upload_max_filesize 1000M";
    //====================================// ERRORS
    public const ERRORS_KEY = "_errors";
    public const ERRORS_FILE = "../errors.log";
    //====================================// MEDIA
    public const MEDIA_KEY = "_media";
    public const MEDIA_DIR = "../media/";
    //====================================// CONFIGS
    public const CONFIGS_FILE = "../configs.php";
    public const CONFIGS_CONTENT = "\n<?php\n// project title\nmzAPI::\$TITLE = 'Project';\n// project debug mode\nmzAPI::\$DEBUG = false;\n// connections max\nmzAPI::\$MAX_CONN_PER_HOUR = 0;\nmzAPI::\$MAX_CONN_PER_MIN = 0;\nmzAPI::\$MAX_CONN_PER_MIN = 0;\n// Databases\nmzAPI::DB(\n\t'main',\n\tnew mzDatabase(\n\t\t'mysql',\n\t\t'localhost',\n\t\t'dbname',\n\t\t'username',\n\t\t'password',\n\t\tmzParams::headers('User-Timezone')\n\t)\n);";
    //====================================// HANDLERS
    public const HANDLERS_DIR = "../handlers/";
    public const HANDLERS_FILE = "../handlers/example.php";
    public const HANDLERS_CONTENT = "<?php\nprint('hello');";
    //====================================// INCLUDES
    public const INCLUDES_DIR = "../includes/";
    public const INCLUDES_FILE = "../includes/example.php";
    public const INCLUDES_CONTENT = "<?php\nprint('hello');";
    //====================================// DOCS
    public const DOCS_KEY = "_docs";
    public const DOCS_DIR = "docs/";
    public const DOCS_FILE = "docs/index.php";
    //====================================// TOOLS
    public const TOOLS_DIR = "tools/";
    //====================================// URL
    public const GETKEY = "mzAPIURL";
    static public string $URL_GET = "";
    static public string $URL_ROOT = "";
    static public string $URL_FILE = "";
    static public string $URL_PARAMS = "";
    static public string $URL_FULL = "";
    //====================================// CONN_PER
    static public float $CONN_TIME = 0;
    static public int $CONN_PER_HOUR = 0;
    static public int $CONN_PER_MIN = 0;
    static public int $CONN_PER_SEC = 0;
    //
    static public ?int $MAX_CONN_PER_HOUR = null;
    static public ?int $MAX_CONN_PER_MIN = null;
    static public ?int $MAX_CONN_PER_SEC = null;
    //====================================// DB
    static public function DB(string $name, mzDatabase $database = null): ?mzDatabase
    {
        if (!empty($name) && !empty($database)) {
            mzAPI::$DBS[$name] = $database;
        }
        return @mzAPI::$DBS[$name];
    }

    //====================================// return
    static function return(int $status = null, string $error = null, string $message = null, $data = null): object
    { // for funcitons
        return (object) ["status" => $status, "error" => $error, "message" => $message, "data" => $data];
    }

    //====================================// response
    static function response(int $status = null, string $error = null, string $message = null, $data = null, $x = null): void
    {
        $response = [];
        $response["status"] = $status;
        $response["statusText"] = mzAPI::STATUSES[$status];
        $response["error"] = $error;
        $response["message"] = $message;
        $response["data"] = $data;
        if (mzAPI::$DEBUG == true) {
            $response += [
                "DEBUG" => [
                    "URL" => mzAPI::$URL_FULL,
                    "STATUSES" => mzAPI::STATUSES,
                    "CONNECTIONS" => [
                        "LastHour" => mzAPI::$CONN_PER_HOUR,
                        "LastMin" => mzAPI::$CONN_PER_MIN,
                        "LastSec" => mzAPI::$CONN_PER_SEC,
                    ],
                    "STATS" => [
                        "CodeExecutionTime-MS" => number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2, ".", ""),
                        "PeakMemory-KB" => number_format(memory_get_peak_usage(false) / 1000, 2, ".", ""),
                        "EndMemory-KB" => number_format(memory_get_usage(false) / 1000, 2, ".", ""),
                    ],
                    "SETTINGS" => [
                        "MaxConnectionsPerHour" => mzAPI::$MAX_CONN_PER_HOUR,
                        "MaxConnectionsPerMin" => mzAPI::$MAX_CONN_PER_MIN,
                        "MaxConnectionsPerSec" => mzAPI::$MAX_CONN_PER_SEC,
                        "MaxExecutionTime-SEC" => ini_get('max_execution_time'),
                        "MaxMemory-MB" => rtrim(ini_get('memory_limit'), "M"),
                        "MaxFileUploads" => ini_get('max_file_uploads'),
                        "MaxInputVars" => ini_get('max_input_vars'),
                    ]
                ]
            ];
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit(gc_collect_cycles());
    }

    //====================================// html_response
    static function html_response(int $status): void
    {
        header("Content-Type: text/html; charset=utf-8");
        header("HTTP/1.0 $status " . @mzAPI::STATUSES[$status]);
        echo "<h1>$status " . @mzAPI::STATUSES[$status] . "</h1>";
        exit();
    }

    //====================================// tools
    static function tools(array $tools = null): void
    {
        foreach ($tools as $tool) {
            mzAPI::inc(mzAPI::TOOLS_DIR . $tool . ".php");
        }
    }

    //====================================// Includes
    static function includes(array $includes = null): void
    {
        foreach ($includes as $include) {
            mzAPI::inc($include . ".php");
        }
    }

    //====================================// Errors
    static public function errors_handler(int $errno, string $errstr, string $errfile, int $errline, array $errcontext): ?bool
    {
        // error was suppressed with the @-operator
        if (!(error_reporting() & $errno)) return false;
        $errortype = "unkown";
        switch ($errno) {
            case E_ERROR:
                $errortype = "ErrorException";
                break;
            case E_WARNING:
                $errortype = "WarningException";
                break;
            case E_PARSE:
                $errortype = "ParseException";
                break;
            case E_NOTICE:
                $errortype = "NoticeException";
                break;
            case E_CORE_ERROR:
                $errortype = "CoreErrorException";
                break;
            case E_CORE_WARNING:
                $errortype = "CoreWarningException";
                break;
            case E_COMPILE_ERROR:
                $errortype = "CompileErrorException";
                break;
            case E_COMPILE_WARNING:
                $errortype = "CompileWarningException";
                break;
            case E_USER_ERROR:
                $errortype = "UserErrorException";
                break;
            case E_USER_WARNING:
                $errortype = "UserWarningException";
                break;
            case E_USER_NOTICE:
                $errortype = "UserNoticeException";
                break;
            case E_STRICT:
                $errortype = "StrictException";
                break;
            case E_RECOVERABLE_ERROR:
                $errortype = "RecoverableErrorException";
                break;
            case E_DEPRECATED:
                $errortype = "DeprecatedException";
                break;
            case E_USER_DEPRECATED:
                $errortype = "UserDeprecatedException";
                break;
        }
        //
        mzAPI::response(500, $errortype, null, [
            "severity" => $errno,
            "message" => htmlspecialchars($errstr),
            "file" => $errfile,
            "line" => $errline,
        ]);
        //====================================// 
        return true;
    }

    //====================================// Include
    static function inc(string $path = null): bool
    {
        if (is_file($path)) {
            include_once($path);
            return true;
        }
        return false;
    }

    //====================================// run
    static function run(): void
    {
        //====================================// connection
        mzAPI::$CONN_TIME = microtime(true);
        //====================================// session
        if (empty(@$_SESSION['LINKS'])) $_SESSION['LINKS'] = [];
        $_SESSION['LINKS'][] = mzAPI::$CONN_TIME;
        //====================================// connection limit
        // calc script
        $times = new stdClass;
        $times->s = mzAPI::$CONN_TIME - (1);
        $times->m = mzAPI::$CONN_TIME - (1 * 60);
        $times->h = mzAPI::$CONN_TIME - (1 * 60 * 60);
        foreach ($_SESSION['LINKS'] as $i => $link) {
            if ($times->s <= $link) mzAPI::$CONN_PER_SEC++;
            if ($times->m <= $link) mzAPI::$CONN_PER_MIN++;
            if ($times->h <= $link) mzAPI::$CONN_PER_HOUR++;
            if ($times->h > $link) unset($_SESSION['LINKS'][$i]);
        }
        //====================================// URL
        mzAPI::$URL_GET = (!empty($_GET[mzAPI::GETKEY]) ? $_GET[mzAPI::GETKEY] : "");
        // file
        mzAPI::$URL_FILE = mzAPI::$URL_GET;
        mzAPI::$URL_ROOT = @explode("/", mzAPI::$URL_FILE)[0];
        // params
        mzAPI::$URL_PARAMS = "";
        foreach ($_GET as $k => $v) {
            if ($k != mzAPI::GETKEY) {
                if (is_array($v)) {
                    foreach ($v as $i => $vv) mzAPI::$URL_PARAMS .= "$k=$vv&";
                } else mzAPI::$URL_PARAMS .= "$k=$v&";
            }
        }
        if (!empty(mzAPI::$URL_PARAMS)) mzAPI::$URL_PARAMS = "?" . rtrim(mzAPI::$URL_PARAMS, "&");
        // full
        mzAPI::$URL_FULL = mzAPI::$URL_GET . mzAPI::$URL_PARAMS;

        //====================================// HTACCESS
        if (!is_file(mzAPI::HTACCESS_FILE)) {
            fopen(mzAPI::HTACCESS_FILE, "w");
            file_put_contents(mzAPI::HTACCESS_FILE, mzAPI::HTACCESS_CONTENT);
        }
        //====================================// MEDIA
        if (!is_dir(mzAPI::MEDIA_DIR)) {
            mkdir(mzAPI::MEDIA_DIR, 0777, true);
        }
        //====================================// HANDLERS
        if (!is_dir(mzAPI::HANDLERS_DIR)) {
            if (mkdir(mzAPI::HANDLERS_DIR, 0777, true)) {
                fopen(mzAPI::HANDLERS_FILE, "w");
                file_put_contents(mzAPI::HANDLERS_FILE, mzAPI::HANDLERS_CONTENT);
            }
        }
        //====================================// CONFIGS
        if (!is_file(mzAPI::CONFIGS_FILE)) {
            fopen(mzAPI::CONFIGS_FILE, "w");
            file_put_contents(mzAPI::CONFIGS_FILE, mzAPI::CONFIGS_CONTENT);
        }
        include_once(mzAPI::CONFIGS_FILE);
        //====================================// session max
        if ((!empty(mzAPI::$MAX_CONN_PER_SEC) && mzAPI::$CONN_PER_SEC > mzAPI::$MAX_CONN_PER_SEC)
            || (!empty(mzAPI::$MAX_CONN_PER_MIN) && mzAPI::$CONN_PER_MIN > mzAPI::$MAX_CONN_PER_MIN)
            || (!empty(mzAPI::$MAX_CONN_PER_HOUR) && mzAPI::$CONN_PER_HOUR > mzAPI::$MAX_CONN_PER_HOUR)
        ) {
            if (mzAPI::$URL_ROOT == mzAPI::MEDIA_KEY) {
                mzAPI::html_response(429);
            } else if (mzAPI::$URL_ROOT == mzAPI::DOCS_KEY) {
                mzAPI::html_response(429);
            } else {
                mzAPI::response(429);
            }
        }
        //========================================// run
        if (mzAPI::$URL_ROOT == mzAPI::MEDIA_KEY) {
            $media = mzAPI::MEDIA_DIR . ltrim(mzAPI::$URL_FILE, mzAPI::MEDIA_KEY);
            if (is_file($media)) {
                header("Content-Type: " . mime_content_type($media));
                header("Content-Length: " . filesize($media));
                readfile($media);
            } else {
                mzAPI::html_response(404);
            }
        } else if (mzAPI::$URL_ROOT == mzAPI::DOCS_KEY) {
            if (!mzAPI::inc(mzAPI::DOCS_FILE)) mzAPI::html_response(404);
        } else if (mzAPI::$URL_ROOT == mzAPI::ERRORS_KEY) {
            $errors = mzAPI::ERRORS_FILE;
            if (is_file($errors) && array_key_exists("clear", $_GET)) {
                file_put_contents($errors, "");
            }
            if (is_file($errors)) {
                echo file_get_contents($errors);
            }
        } else {
            $file = mzAPI::HANDLERS_DIR . mzAPI::$URL_FILE . ".php";
            if (!mzAPI::inc($file)) mzAPI::response(404);
        }
        exit();
    }
}
