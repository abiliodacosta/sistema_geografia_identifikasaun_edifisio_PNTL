<?php
require 'admin/koneksaun.php';
echo "--- tb_suco ---\n";
$res = mysqli_query($conn, "DESCRIBE tb_suco");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . "\n"; }
echo "\n--- tb_postu_administrativu ---\n";
$res = mysqli_query($conn, "DESCRIBE tb_postu_administrativu");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . "\n"; }
echo "\n--- tb_munisipio ---\n";
$res = mysqli_query($conn, "DESCRIBE tb_munisipio");
while($row = mysqli_fetch_assoc($res)) { echo $row['Field'] . "\n"; }
