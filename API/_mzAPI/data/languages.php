<?php
mzAPI::response(200, null, null, json_decode(file_get_contents("json/languages.json", true), true));
