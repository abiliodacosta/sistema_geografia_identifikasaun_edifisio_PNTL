<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    echo "<script>window.location='?pntl=postu_administrativu/view';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_munisipio = intval($_POST['id_munisipio']);
    $naran_posto = htmlspecialchars(trim($_POST['naran_posto']));

    if (!empty($naran_posto) && $id_munisipio > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE tb_postu_administrativu SET id_munisipio = ?, naran_posto = ? WHERE id_posto = ?");
        mysqli_stmt_bind_param($stmt, "isi", $id_munisipio, $naran_posto, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Altera_Postu');
            echo "<script>window.location='?pntl=postu_administrativu/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Altera');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_postu_administrativu WHERE id_posto = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    echo "<script>window.location='?pntl=postu_administrativu/view';</script>";
    exit;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Altera_Postu') ?></h6>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="id_munisipio" class="form-label"><?= __('Munisipiu') ?></label>
                        <select class="form-select" id="id_munisipio" name="id_munisipio" required>
                            <option value=""><?= __('Hili_Munisipiu') ?></option>
                            <?php
                            $q_munisipio = mysqli_query($conn, "SELECT * FROM tb_munisipio ORDER BY naran_munisipio ASC");
                            while ($row = mysqli_fetch_array($q_munisipio)) {
                                $selected = ($row['id_munisipio'] == $data['id_munisipio']) ? "selected" : "";
                                echo "<option value='".$row['id_munisipio']."' ".$selected.">".htmlspecialchars($row['naran_munisipio'])."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="naran_posto" class="form-label"><?= __('Naran_Postu') ?></label>
                        <input type="text" class="form-control" id="naran_posto" name="naran_posto" value="<?php echo htmlspecialchars($data['naran_posto']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> <?= __('Altera_Dadus') ?></button>
                    <a href="?pntl=postu_administrativu/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
