<?php
$langs = ['tet', 'pt', 'en'];
$success = true;

foreach ($langs as $l) {
    $default_file = "../lang/default_{$l}.json";
    $active_file = "../lang/{$l}.json";
    
    if (file_exists($default_file)) {
        if (!copy($default_file, $active_file)) {
            $success = false;
        }
    }
}

if ($success) {
    $_SESSION['success'] = "Sistema Lingua reset fali ba padraun orijinál ho susesu!";
} else {
    $_SESSION['error'] = "Iha problema ruma wainhira halo reset ba faile lingua sira.";
}

echo "<script>window.location='?pntl=lingua/view';</script>";
?>
