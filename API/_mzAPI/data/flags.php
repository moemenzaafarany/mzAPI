<?php
// params
mzAPI::tools(['mzParams']);
mzParams::addString("size", null, true, true, false, false, null, null, null, ["16", "24", "32", "48", "64", "128"]);
if (!empty(mzParams::errors())) mzAPI::response(400, "params", null, mzParams::errors());
// success
mzAPI::response(200, null, null, json_decode(file_get_contents("json/flags-" . mzParams::params("size") . ".json", true), true));
