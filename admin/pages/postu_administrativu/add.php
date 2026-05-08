<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_munisipio = intval($_POST['id_munisipio']);
    $naran_posto = htmlspecialchars(trim($_POST['naran_posto']));

    if (!empty($naran_posto) && $id_munisipio > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_postu_administrativu (id_munisipio, naran_posto) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $id_munisipio, $naran_posto);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama_Postu');
            echo "<script>window.location='?pntl=postu_administrativu/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Hatama');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Aumenta_Postu') ?></h6>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="id_munisipio" class="form-label"><?= __('Munisipiu') ?></label>
                        <select class="form-select" id="id_munisipio" name="id_munisipio" required>
                            <option value=""><?= __('Hili_Munisipiu') ?></option>
                            <?php
                            $q_munisipio = mysqli_query($conn, "SELECT * FROM tb_munisipio ORDER BY naran_munisipio ASC");
                            while ($row = mysqli_fetch_array($q_munisipio)) {
                                echo "<option value='".$row['id_munisipio']."'>".htmlspecialchars($row['naran_munisipio'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="naran_posto" class="form-label"><?= __('Naran_Postu') ?></label>
                        <input type="text" class="form-control" id="naran_posto" name="naran_posto" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= __('Rai_Dadus') ?></button>
                    <a href="?pntl=postu_administrativu/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
