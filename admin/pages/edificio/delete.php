<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt_get = mysqli_prepare($conn, "SELECT foto FROM tb_edifisio_pntl WHERE id_edifisio = ?");
    mysqli_stmt_bind_param($stmt_get, "i", $id);
    mysqli_stmt_execute($stmt_get);
    $result = mysqli_stmt_get_result($stmt_get);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt_get);

    $stmt = mysqli_prepare($conn, "DELETE FROM tb_edifisio_pntl WHERE id_edifisio = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        if (!empty($data['foto']) && file_exists("uploads/edifisio/" . $data['foto'])) {
            unlink("uploads/edifisio/" . $data['foto']);
        }
        $_SESSION['success'] = __('Susesu_Hamos_Edifisio');
        header("Location: ?pntl=edificio/view");
        exit();
    } else {
        $error_msg = mysqli_stmt_error($stmt);
        $_SESSION['error'] = __('Faila_Hamos') . " Erru: " . $error_msg;
        header("Location: ?pntl=edificio/view");
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    echo "<script>window.location='?pntl=edificio/view';</script>";
}
?>
