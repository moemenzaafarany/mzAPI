<?php
mzAPI::response(200, null, null, json_decode(file_get_contents("json/continents.json",true), true));
