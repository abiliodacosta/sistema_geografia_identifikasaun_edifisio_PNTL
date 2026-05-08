<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Preveni hamos admin ida ne'ebé uza hela (optional, but good for safety)
    if ($id == $_SESSION['id']) {
        $_SESSION['warning'] = __('Labele_Hamos_An_Rasik');
        echo "<script>window.location='?pntl=admin/view';</script>";
        exit;
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM tb_admin WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = __('Susesu_Hamos_Admin');
        echo "<script>window.location='?pntl=admin/view';</script>";
    } else {
        $_SESSION['error'] = __('Faila_Hamos_Admin');
        echo "<script>window.location='?pntl=admin/view';</script>";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>window.location='?pntl=admin/view';</script>";
}
?>
