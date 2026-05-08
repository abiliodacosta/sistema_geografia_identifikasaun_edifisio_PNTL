<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran_tipu = htmlspecialchars(trim($_POST['naran_tipu']));
    $naran_tipu_pt = htmlspecialchars(trim($_POST['naran_tipu_pt']));
    $naran_tipu_en = htmlspecialchars(trim($_POST['naran_tipu_en']));
    $diskrisaun = htmlspecialchars(trim($_POST['diskrisaun']));
    $diskrisaun_pt = htmlspecialchars(trim($_POST['diskrisaun_pt']));
    $diskrisaun_en = htmlspecialchars(trim($_POST['diskrisaun_en']));

    if (!empty($naran_tipu)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_tipo_edifisio (naran_tipu, naran_tipu_pt, naran_tipu_en, diskrisaun, diskrisaun_pt, diskrisaun_en) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $naran_tipu, $naran_tipu_pt, $naran_tipu_en, $diskrisaun, $diskrisaun_pt, $diskrisaun_en);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama_Tipu');
            echo "<script>window.location='?pntl=tipo_edifisio/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Hatama');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Tipu');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-8 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Aumenta_Tipu') ?></h6>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu" class="form-label"><?= __('Naran_Tipu') ?> (TET)</label>
                            <input type="text" class="form-control" id="naran_tipu" name="naran_tipu" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu_pt" class="form-label"><?= __('Naran_Tipu') ?> (PT)</label>
                            <input type="text" class="form-control" id="naran_tipu_pt" name="naran_tipu_pt">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="naran_tipu_en" class="form-label"><?= __('Naran_Tipu') ?> (EN)</label>
                            <input type="text" class="form-control" id="naran_tipu_en" name="naran_tipu_en">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="diskrisaun" class="form-label"><?= __('Diskrisaun') ?> (TET)</label>
                        <textarea class="form-control" id="diskrisaun" name="diskrisaun" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="diskrisaun_pt" class="form-label"><?= __('Diskrisaun') ?> (PT)</label>
                        <textarea class="form-control" id="diskrisaun_pt" name="diskrisaun_pt" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="diskrisaun_en" class="form-label"><?= __('Diskrisaun') ?> (EN)</label>
                        <textarea class="form-control" id="diskrisaun_en" name="diskrisaun_en" rows="2"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= __('Rai_Dadus') ?></button>
                    <a href="?pntl=tipo_edifisio/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>