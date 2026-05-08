<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM tb_tipo_edifisio WHERE id_tipo = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = __('Susesu_Hamos');
        echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
    } else {
        $_SESSION['error'] = __('Faila_Hamos_Relasiona');
        echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
}
?>
