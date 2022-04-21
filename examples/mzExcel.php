<?php
mzAPI::tools(['mzExcel']);
//
$xlsx = new mzExcel();
for ($s = 1; $s <= 3; $s++) {
    $sheet = [['<blue><b>' . "header $s"]];
    for ($r = 1; $r <= 10; $r++) {
        $sheet[] = ['<blue>' . "row $r"];
    }
    $xlsx->addSheet($sheet, "Sheet-$s");
}
$xlsx->downloadAs('download.xlsx');
exit();
