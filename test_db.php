<?php
require 'admin/koneksaun.php';
$markers_data = [];
$q_map = mysqli_query($conn, "SELECT e.*, s.naran_suco, p.naran_posto, m.naran_munisipio, t.naran_tipu, t.naran_tipu_pt, t.naran_tipu_en 
                              FROM tb_edifisio_pntl e 
                              LEFT JOIN tb_suco s ON e.id_suco = s.id_suco
                              LEFT JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto
                              LEFT JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio
                              LEFT JOIN tb_tipo_edifisio t ON e.id_tipo = t.id_tipo");

if (!$q_map) {
    echo "SQL ERROR: " . mysqli_error($conn) . "\n";
} else {
    while ($row = mysqli_fetch_assoc($q_map)) {
        $markers_data[] = $row;
    }
    echo "Count: " . count($markers_data) . "\n";
    $json = json_encode($markers_data);
    if ($json === false) {
        echo "JSON Encode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON OK\n";
    }
}
