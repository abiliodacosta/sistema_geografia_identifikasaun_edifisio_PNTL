<?php
require_once 'admin/koneksaun.php';
$tables = ['tb_munisipio', 'tb_postu_administrativu', 'tb_suco'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    $result = mysqli_query($conn, "DESCRIBE $table");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- {$row['Field']}\n";
    }
    echo "\n";
}
?>
