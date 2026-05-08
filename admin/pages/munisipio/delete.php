<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // 1. Delete all related photos first from tb_edifisio_pntl
    $query_fotos = "SELECT e.foto FROM tb_edifisio_pntl e 
                    JOIN tb_suco s ON e.id_suco = s.id_suco 
                    JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto 
                    WHERE p.id_munisipio = ?";
    $stmt_fotos = mysqli_prepare($conn, $query_fotos);
    mysqli_stmt_bind_param($stmt_fotos, "i", $id);
    mysqli_stmt_execute($stmt_fotos);
    $result_fotos = mysqli_stmt_get_result($stmt_fotos);
    while ($row_foto = mysqli_fetch_assoc($result_fotos)) {
        if (!empty($row_foto['foto']) && file_exists("uploads/edifisio/" . $row_foto['foto'])) {
            unlink("uploads/edifisio/" . $row_foto['foto']);
        }
    }
    mysqli_stmt_close($stmt_fotos);

    // 2. Delete from tb_edifisio_pntl
    $query_del_edifisio = "DELETE e FROM tb_edifisio_pntl e 
                           JOIN tb_suco s ON e.id_suco = s.id_suco 
                           JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto 
                           WHERE p.id_munisipio = ?";
    $stmt_del_e = mysqli_prepare($conn, $query_del_edifisio);
    mysqli_stmt_bind_param($stmt_del_e, "i", $id);
    mysqli_stmt_execute($stmt_del_e);
    mysqli_stmt_close($stmt_del_e);

    // 3. Delete from tb_suco
    $query_del_suco = "DELETE s FROM tb_suco s 
                       JOIN tb_postu_administrativu p ON s.id_posto = p.id_posto 
                       WHERE p.id_munisipio = ?";
    $stmt_del_s = mysqli_prepare($conn, $query_del_suco);
    mysqli_stmt_bind_param($stmt_del_s, "i", $id);
    mysqli_stmt_execute($stmt_del_s);
    mysqli_stmt_close($stmt_del_s);

    // 4. Delete from tb_postu_administrativu
    $query_del_postu = "DELETE FROM tb_postu_administrativu WHERE id_munisipio = ?";
    $stmt_del_p = mysqli_prepare($conn, $query_del_postu);
    mysqli_stmt_bind_param($stmt_del_p, "i", $id);
    mysqli_stmt_execute($stmt_del_p);
    mysqli_stmt_close($stmt_del_p);

    // 5. Finally delete the munisipio
    $stmt = mysqli_prepare($conn, "DELETE FROM tb_munisipio WHERE id_munisipio = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = __('Susesu_Hamos_Munisipiu');
        echo "<script>window.location='?pntl=munisipio/view';</script>";
    } else {
        $_SESSION['error'] = __('Faila_Hamos_Munisipiu');
        echo "<script>window.location='?pntl=munisipio/view';</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>window.location='?pntl=munisipio/view';</script>";
}
?>