<?php
$keys_tet = [
    "Laiha_Enderesu" => "Laiha enderesu",
    "Edifisio_PNTL" => "Edifísiu PNTL"
];
$keys_pt = [
    "Laiha_Enderesu" => "Sem endereço",
    "Edifisio_PNTL" => "Edifício PNTL"
];
$keys_en = [
    "Laiha_Enderesu" => "No address",
    "Edifisio_PNTL" => "PNTL Building"
];

foreach(["tet", "pt", "en"] as $lang) { 
    $f = "c:\\xampp\\htdocs\\pntl-app\\lang\\$lang.json"; 
    $j = json_decode(file_get_contents($f), true); 
    $n = ${"keys_$lang"}; 
    foreach($n as $k=>$v) {
        $j[$k] = $v;
    }
    file_put_contents($f, json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
    file_put_contents("c:\\xampp\\htdocs\\pntl-app\\lang\\default_$lang.json", json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); 
} 
echo "Done";
