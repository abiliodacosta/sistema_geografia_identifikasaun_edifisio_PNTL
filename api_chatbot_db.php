<?php
header('Content-Type: application/json');
require 'admin/koneksaun.php';

$data = [];

// Get total buildings
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_edifisio_pntl");
$data['total_edifisio'] = mysqli_fetch_assoc($res)['total'];

// Get total municipalities
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_munisipio");
$data['total_munisipio'] = mysqli_fetch_assoc($res)['total'];

// Get total posts
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_postu_administrativu");
$data['total_postu'] = mysqli_fetch_assoc($res)['total'];

// Get total suco
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_suco");
$data['total_suco'] = mysqli_fetch_assoc($res)['total'];

// Get building counts by municipality
$query_muni = "SELECT m.naran_munisipio, COUNT(e.id_edifisio) as total 
               FROM tb_munisipio m 
               LEFT JOIN tb_postu_administrativu p ON m.id_munisipio = p.id_munisipio
               LEFT JOIN tb_suco s ON p.id_posto = s.id_posto
               LEFT JOIN tb_edifisio_pntl e ON s.id_suco = e.id_suco
               GROUP BY m.id_munisipio";
$res_muni = mysqli_query($conn, $query_muni);
$muni_data = [];
if($res_muni) {
    while($row = mysqli_fetch_assoc($res_muni)) {
        $muni_data[$row['naran_munisipio']] = $row['total'];
    }
}
$data['edifisio_por_munisipio'] = $muni_data;

echo json_encode($data);
?>
