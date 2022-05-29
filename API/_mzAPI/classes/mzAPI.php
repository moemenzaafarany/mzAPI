<?php
/* 1.0.0 */
class mzAPI
{
    //====================================// constants
    public const VERSION = "1";
    //====================================// HTACCESS
    private const HTACCESS_FILE = "../.htaccess";
    private const HTACCESS_CONTENT = "content/HTACCESS_CONTENT.txt";
    //====================================// PHPINI
    private const PHPINI_FILE = "php.ini";
    private const PHPINI_CONTENT = "content/PHPINI_CONTENT.txt";
    //====================================// USERINI
    private const USERINI_FILE = ".user.ini";
    private const USERINI_CONTENT = "content/PHPINI_CONTENT.txt";
    //====================================// CONFIGS
    private const CONFIGS_FILE = "../configs.php";
    private const CONFIGS_CONTENT = "content/CONFIGS_CONTENT.txt";
    //====================================// MEDIA
    private const MEDIA_KEY = "_media";
    public const MEDIA_DIR = "../media/";
    //====================================// DOCS
    private const DOCS_KEY = "_docs";
    private const DOCS_FILE = "docs/index.php";
    //====================================// ERRORS
    private const ERRORS_KEY = "_errors";
    private const ERRORS_CLEAR_KEY = "_errors/clear";
    private const ERRORS_FILE = "errors.log";
    //====================================// DATA
    private const DATA_KEY = "_data";
    private const DATA_DIR = "data/";
    private const DATA_FILES = ["continents", "languages", "currencies", "countries", "cities"];
    //====================================// DEBUG
    private const DEBUG_KEY = "_debug";
    //====================================// HANDLERS
    private const HANDLERS_DIR = "../handlers/";
    private const HANDLERS_FILE = "../handlers/example.php";
    private const HANDLERS_CONTENT = "<?php\nprint('hello');";
    //====================================// INCLUDES
    public const INCLUDES_DIR = "../includes/";
    private const INCLUDES_FILE = "../includes/example.php";
    private const INCLUDES_CONTENT = "<?php\nprint('hello');";
    //====================================// TOOLS
    public const TOOLS_DIR = "tools/";
    //====================================// classes
    public const CLASSES_DIR = "classes/";
    //====================================// URL
    public const GETKEY = "mzAPIURL";
    //====================================// variables
    // private
    static private bool $DEBUG = false;
    static private float $CONN_TIME = 0;
    static private int $CONNS_PER_HOUR = 0;
    static private int $CONNS_PER_MIN = 0;
    static private int $CONNS_PER_SEC = 0;
    // public
    static public string $TITLE = "new Project";
    static public string $VERSION = "1";
    static public array $DBS = [];
    //====================================// URL
    static public string $URL_GET = "";
    static public string $URL_ROOT = "";
    static public string $URL_FILE = "";
    static public string $URL_PARAMS = "";
    static public string $URL_FULL = "";
    //====================================// CONN
    // MAX
    static public ?int $MAX_CONNS_PER_HOUR = null;
    static public ?int $MAX_CONNS_PER_MIN = null;
    static public ?int $MAX_CONNS_PER_SEC = null;
    //====================================// Private
    //====================================// html_response
    static private function _html_response(int $status): void
    {
        header("Content-Type: text/html; charset=utf-8");
        header("HTTP/1.0 $status " . @mzRes::HTTP_STATUSES[$status]);
        echo "<h1>$status " . @mzRes::HTTP_STATUSES[$status] . "</h1>";
        exit();
    }
    //====================================// Include
    static public function _inc(string $path = null): bool
    {
        if (is_file($path)) {
            include_once($path);
            return true;
        }
        return false;
    }

    //====================================// Public
    //====================================// DB
    static public function DB(string $name, mzDatabase $database = null): ?mzDatabase
    {
        if (!empty($name) && !empty($database)) {
            mzAPI::$DBS[$name] = $database;
        }
        return @mzAPI::$DBS[$name];
    }

    //====================================// response
    static public function response(int $status = null, string $error = null, string $message = null, $data = null, $x = null): void
    {
        $response = [];
        $response["status"] = $status;
        $response["statusText"] = @mzRes::HTTP_STATUSES[$status];
        $response["error"] = $error;
        $response["message"] = $message;
        $response["data"] = $data;
        if (mzAPI::$DEBUG == true) {
            $response += [
                "DEBUG" => [
                    "URL" => mzAPI::$URL_FULL,
                    "CONNECTIONS" => [
                        "LastHour" => mzAPI::$CONNS_PER_HOUR,
                        "LastMin" => mzAPI::$CONNS_PER_MIN,
                        "LastSec" => mzAPI::$CONNS_PER_SEC,
                    ],
                    "STATS" => [
                        "CodeExecutionTime-MS" => number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2, ".", ""),
                        "PeakMemory-KB" => number_format(memory_get_peak_usage(false) / 1000, 2, ".", ""),
                        "EndMemory-KB" => number_format(memory_get_usage(false) / 1000, 2, ".", ""),
                    ],
                    "SETTINGS" => [
                        "MaxConnectionsPerHour" => mzAPI::$MAX_CONNS_PER_HOUR,
                        "MaxConnectionsPerMin" => mzAPI::$MAX_CONNS_PER_MIN,
                        "MaxConnectionsPerSec" => mzAPI::$MAX_CONNS_PER_SEC,
                        "MaxExecutionTime-SEC" => ini_get('max_execution_time'),
                        "MaxMemory-MB" => rtrim(ini_get('memory_limit'), "M"),
                        "MaxFileUploads" => ini_get('max_file_uploads'),
                        "MaxInputVars" => ini_get('max_input_vars'),
                    ],
                    "HTTP_STATUSES" => mzRes::HTTP_STATUSES,
                ]
            ];
        }
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit(gc_collect_cycles());
    }

    //====================================// tools
    static public function tools(array $tools = null): void
    {
        foreach ($tools as $tool) {
            mzAPI::_inc(mzAPI::TOOLS_DIR . $tool . ".php");
        }
    }

    //====================================// Includes
    static public function includes(array $includes = null): void
    {
        foreach ($includes as $include) {
            mzAPI::_inc(mzAPI::INCLUDES_DIR . $include . ".php");
        }
    }

    /*
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
    */

    //====================================// init
    static public function init(): void
    {
        //====================================// ini
        ini_set("error_log", mzAPI::ERRORS_FILE);
        //====================================// connection
        mzAPI::$CONN_TIME = microtime(true);
        //====================================// session
        if (empty(@$_SESSION['LINKS'])) $_SESSION['LINKS'] = [];
        $_SESSION['LINKS'][] = mzAPI::$CONN_TIME;
        //====================================// connection limit
        // calc script
        $times = new stdClass;
        $times->s = mzAPI::$CONN_TIME - 1;
        $times->m = mzAPI::$CONN_TIME - 60;
        $times->h = mzAPI::$CONN_TIME - 3600;
        foreach ($_SESSION['LINKS'] as $i => $link) {
            if ($times->s <= $link) mzAPI::$CONNS_PER_SEC++;
            if ($times->m <= $link) mzAPI::$CONNS_PER_MIN++;
            if ($times->h <= $link) mzAPI::$CONNS_PER_HOUR++;
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
    }
    //====================================// run
    static public function run(): void
    {
        //====================================// HTACCESS
        if (!is_file(mzAPI::HTACCESS_FILE) && is_file(mzAPI::HTACCESS_CONTENT)) {
            fopen(mzAPI::HTACCESS_FILE, "w");
            file_put_contents(mzAPI::HTACCESS_FILE, file_get_contents(mzAPI::HTACCESS_CONTENT));
        }
        //====================================// PHPINI
        if (!is_file(mzAPI::PHPINI_FILE) && is_file(mzAPI::PHPINI_CONTENT)) {
            fopen(mzAPI::PHPINI_FILE, "w");
            file_put_contents(mzAPI::PHPINI_FILE, file_get_contents(mzAPI::PHPINI_CONTENT));
        }
        //====================================// USERINI
        if (!is_file(mzAPI::USERINI_FILE) && is_file(mzAPI::USERINI_CONTENT)) {
            fopen(mzAPI::USERINI_FILE, "w");
            file_put_contents(mzAPI::USERINI_FILE, file_get_contents(mzAPI::USERINI_CONTENT));
        }
        //====================================// CONFIGS
        if (!is_file(mzAPI::CONFIGS_FILE) && is_file(mzAPI::CONFIGS_CONTENT)) {
            fopen(mzAPI::CONFIGS_FILE, "w");
            file_put_contents(mzAPI::CONFIGS_FILE, file_get_contents(mzAPI::CONFIGS_CONTENT));
        }
        include_once(mzAPI::CONFIGS_FILE);
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
        //====================================// INCLUDES
        if (!is_dir(mzAPI::INCLUDES_DIR)) {
            if (mkdir(mzAPI::INCLUDES_DIR, 0777, true)) {
                fopen(mzAPI::INCLUDES_FILE, "w");
                file_put_contents(mzAPI::INCLUDES_FILE, mzAPI::INCLUDES_CONTENT);
            }
        }
        //====================================// Max Connections
        if ((!empty(mzAPI::$MAX_CONNS_PER_SEC) && mzAPI::$CONNS_PER_SEC > mzAPI::$MAX_CONNS_PER_SEC)
            || (!empty(mzAPI::$MAX_CONNS_PER_MIN) && mzAPI::$CONNS_PER_MIN > mzAPI::$MAX_CONNS_PER_MIN)
            || (!empty(mzAPI::$MAX_CONNS_PER_HOUR) && mzAPI::$CONNS_PER_HOUR > mzAPI::$MAX_CONNS_PER_HOUR)
        ) {
            if (mzAPI::$URL_ROOT == mzAPI::MEDIA_KEY) {
                mzAPI::_html_response(429);
            } else if (mzAPI::$URL_ROOT == mzAPI::DOCS_KEY) {
                mzAPI::_html_response(429);
            } else {
                mzAPI::response(429);
            }
        }
        //========================================// Run
        // media
        if (mzAPI::$URL_ROOT == mzAPI::MEDIA_KEY) {
            $media = mzAPI::MEDIA_DIR . ltrim(mzAPI::$URL_FILE, mzAPI::MEDIA_KEY);
            if (is_file($media)) {
                header("Content-Type: " . mime_content_type($media));
                header("Content-Length: " . filesize($media));
                readfile($media);
            } else {
                mzAPI::_html_response(404);
            }
        }
        // docs
        else if (mzAPI::$URL_ROOT == mzAPI::DOCS_KEY) {
            if (!mzAPI::_inc(mzAPI::DOCS_FILE)) mzAPI::_html_response(404);
        }
        // errors
        else if (mzAPI::$URL_ROOT == mzAPI::ERRORS_KEY) {
            $errors = mzAPI::ERRORS_FILE;
            if (is_file($errors) && mzAPI::$URL_FILE == mzAPI::ERRORS_CLEAR_KEY) {
                file_put_contents($errors, "");
            }
            if (is_file($errors)) {
                echo file_get_contents($errors);
            }
        }
        // data
        else if (mzAPI::$URL_ROOT == mzAPI::DATA_KEY) {
            mzAPI::$DEBUG = true;
            $file = mzAPI::DATA_DIR . ltrim(mzAPI::$URL_FILE, mzAPI::DATA_KEY) . ".php";
            if (!mzAPI::_inc($file)) mzAPI::response(404, null, null, mzAPI::DATA_FILES);
        }
        // debug
        else if (mzAPI::$URL_ROOT == mzAPI::DEBUG_KEY) {
            mzAPI::$DEBUG = true;
            $file = mzAPI::HANDLERS_DIR . ltrim(mzAPI::$URL_FILE, mzAPI::DEBUG_KEY) . ".php";
            if (!mzAPI::_inc($file)) mzAPI::response(404);
        }
        // handlers
        else {
            $file = mzAPI::HANDLERS_DIR . mzAPI::$URL_FILE . ".php";
            if (!mzAPI::_inc($file)) mzAPI::response(404);
        }
        // message for empty
        mzAPI::response(200, null, "no-message", [mzAPI::$URL_ROOT]);
    }

    //========================================// end Class
}
