<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran_tipu = htmlspecialchars(trim($_POST['naran_tipu']));
    $naran_tipu_pt = htmlspecialchars(trim($_POST['naran_tipu_pt']));
    $naran_tipu_en = htmlspecialchars(trim($_POST['naran_tipu_en']));
    $diskrisaun = htmlspecialchars(trim($_POST['diskrisaun']));
    $diskrisaun_pt = htmlspecialchars(trim($_POST['diskrisaun_pt']));
    $diskrisaun_en = htmlspecialchars(trim($_POST['diskrisaun_en']));

    if (!empty($naran_tipu)) {
        $stmt = mysqli_prepare($conn, "UPDATE tb_tipo_edifisio SET naran_tipu = ?, naran_tipu_pt = ?, naran_tipu_en = ?, diskrisaun = ?, diskrisaun_pt = ?, diskrisaun_en = ? WHERE id_tipo = ?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $naran_tipu, $naran_tipu_pt, $naran_tipu_en, $diskrisaun, $diskrisaun_pt, $diskrisaun_en, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Altera_Tipu');
            echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Altera');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Tipu');
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_tipo_edifisio WHERE id_tipo = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
    exit;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-8 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Altera_Tipu') ?></h6>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu" class="form-label"><?= __('Naran_Tipu') ?> (TET)</label>
                            <input type="text" class="form-control" id="naran_tipu" name="naran_tipu" value="<?php echo htmlspecialchars($data['naran_tipu']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu_pt" class="form-label"><?= __('Naran_Tipu') ?> (PT)</label>
                            <input type="text" class="form-control" id="naran_tipu_pt" name="naran_tipu_pt" value="<?php echo htmlspecialchars($data['naran_tipu_pt']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu_en" class="form-label"><?= __('Naran_Tipu') ?> (EN)</label>
                            <input type="text" class="form-control" id="naran_tipu_en" name="naran_tipu_en" value="<?php echo htmlspecialchars($data['naran_tipu_en']); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="diskrisaun" class="form-label"><?= __('Diskrisaun') ?> (TET)</label>
                        <textarea class="form-control" id="diskrisaun" name="diskrisaun" rows="2"><?php echo htmlspecialchars($data['diskrisaun']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="diskrisaun_pt" class="form-label"><?= __('Diskrisaun') ?> (PT)</label>
                        <textarea class="form-control" id="diskrisaun_pt" name="diskrisaun_pt" rows="2"><?php echo htmlspecialchars($data['diskrisaun_pt']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="diskrisaun_en" class="form-label"><?= __('Diskrisaun') ?> (EN)</label>
                        <textarea class="form-control" id="diskrisaun_en" name="diskrisaun_en" rows="2"><?php echo htmlspecialchars($data['diskrisaun_en']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> <?= __('Altera_Dadus') ?></button>
                    <a href="?pntl=tipo_edifisio/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
