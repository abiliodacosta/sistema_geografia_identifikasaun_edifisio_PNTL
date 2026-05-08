<?php
require_once 'koneksaun.php';

$query = mysqli_query($conn, "SELECT id_ajenda as id, titulu as title, CONCAT(data_ajenda, 'T', ora_ajenda) as start, diskrisaun as description FROM tb_ajenda");

$events = [];
while($row = mysqli_fetch_assoc($query)) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events);
?>
