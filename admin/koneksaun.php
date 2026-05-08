<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_localition_pntl';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksaun fali: " . mysqli_connect_error());
}
?>
