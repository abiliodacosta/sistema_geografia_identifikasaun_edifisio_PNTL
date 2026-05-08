<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_posto = intval($_POST['id_posto']);
    $naran_suco = htmlspecialchars(trim($_POST['naran_suco']));

    if (!empty($naran_suco) && $id_posto > 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_suco (id_posto, naran_suco) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $id_posto, $naran_suco);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama_Suku');
            header("Location: ?pntl=suku/view");
            exit();
        } else {
            $_SESSION['error'] = __('Faila_Hatama');
            header("Location: ?pntl=suku/add");
            exit();
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
                <h6 class="mb-4"><?= __('Form_Aumenta_Suku') ?></h6>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="id_posto" class="form-label"><?= __('Postu_Administrativu') ?></label>
                        <select class="form-select" id="id_posto" name="id_posto" required>
                            <option value=""><?= __('Hili_Postu') ?></option>
                            <?php
                            $q_posto = mysqli_query($conn, "SELECT p.*, m.naran_munisipio 
                                                            FROM tb_postu_administrativu p 
                                                            JOIN tb_munisipio m ON p.id_munisipio = m.id_munisipio 
                                                            ORDER BY m.naran_munisipio ASC, p.naran_posto ASC");
                            while ($row = mysqli_fetch_array($q_posto)) {
                                echo "<option value='".$row['id_posto']."'>".htmlspecialchars($row['naran_posto'])." (".htmlspecialchars($row['naran_munisipio']).")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="naran_suco" class="form-label"><?= __('Naran_Suku') ?></label>
                        <input type="text" class="form-control" id="naran_suco" name="naran_suco" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= __('Rai_Dadus') ?></button>
                    <a href="?pntl=suku/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>