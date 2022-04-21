<?php
header("Content-Type: text/html; charset=utf-8");
http_response_code(200);
//====================================// params
$FILES = [];
//====================================// functions
function scan(String $dir, array &$arr)
{
    if (!is_dir($dir)) return null;
    $scan = array_diff(scandir($dir, 1), ['..', '.']);
    foreach ($scan as $f) {
        $f = $dir . $f;
        if (is_file($f) && strpos($f, ".php") != false) {
            $name = str_replace(".php", "", $f);
            $name = str_replace(mzAPI::HANDLERS, "", $name);
            $arr[$name] = file_get_contents($f);
        }
        if (is_dir($f)) scan($f . "/", $arr);
    }
}
//====================================// scan
scan(mzAPI::HANDLERS, $FILES);
ksort($FILES);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation</title>

    <style>
        <?php include_once("style.css") ?>
    </style>
</head>

<body>

    <div class="row">
        <div class="col-1">
            <div class="ebSideNav-container">
                <div class="ebSideNav">
                    <p class="ebSideNav-title">Documentation</p>
                    <div class="ebSideNav-list">
                        <button class="ebSideNav-item" href="#">Home</button>
                        <button class="ebSideNav-item" href="#">Errors</button>
                    </div>
                    <div class="ebSideNav-divider"></div>
                    <p class="ebSideNav-title">APIs</p>
                    <input class="ebSideNav-input" filter-target="#apis-output" placeholder="Search">
                    <div class="ebSideNav-list" id="apis-output">
                        <button class="ebSideNav-item hide" id="apis-sample">sample</button>
                    </div>
                    <div class="ebSideNav-spacer"></div>
                </div>
            </div>
        </div>
        <div class="col-3">

            <div class="params" id="params-output">
                <div class="param hide" id="params-sample">
                    <span data-id="name">name</span>
                    <span data-id="type">type</span>
                    <span data-id="required">required</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const Files = <?= json_encode($FILES, JSON_UNESCAPED_UNICODE) ?>;
        <?php include_once("script.js") ?>
    </script>
</body>

</html>