<?php
require 'admin/koneksaun.php';
$res = mysqli_query($conn, "DESCRIBE tb_edifisio_pntl");
while($row = mysqli_fetch_assoc($res)) {
    echo $row['Field'] . "\n";
}
