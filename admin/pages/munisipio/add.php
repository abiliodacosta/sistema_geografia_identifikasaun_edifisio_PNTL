<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran_munisipio = htmlspecialchars(trim($_POST['naran_munisipio']));

    if (!empty($naran_munisipio)) {
        // Uza prepared statement ba seguransa
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_munisipio (naran_munisipio) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $naran_munisipio);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama');
            echo "<script>window.location='?pntl=munisipio/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Hatama');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Munisipiu');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Aumenta_Munisipiu') ?></h6>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="naran_munisipio" class="form-label"><?= __('Naran_Munisipiu') ?></label>
                        <input type="text" class="form-control" id="naran_munisipio" name="naran_munisipio" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= __('Rai_Dadus') ?></button>
                    <a href="?pntl=munisipio/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>