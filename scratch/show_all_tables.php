<?php
require '../admin/koneksaun.php';
$res = mysqli_query($conn, "SHOW TABLES");
while($row = mysqli_fetch_array($res)) {
    $table = $row[0];
    echo "--- $table ---\n";
    $res2 = mysqli_query($conn, "DESCRIBE $table");
    while($row2 = mysqli_fetch_assoc($res2)) {
        echo $row2['Field'] . "\n";
    }
    echo "\n";
}
?>
