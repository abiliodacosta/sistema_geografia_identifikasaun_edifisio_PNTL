<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    header("Location: ?pntl=suku/view");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_posto = intval($_POST['id_posto']);
    $naran_suco = htmlspecialchars(trim($_POST['naran_suco']));

    if (!empty($naran_suco) && $id_posto > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE tb_suco SET id_posto = ?, naran_suco = ? WHERE id_suco = ?");
        mysqli_stmt_bind_param($stmt, "isi", $id_posto, $naran_suco, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Altera_Suku');
            header("Location: ?pntl=suku/view");
            exit();
        } else {
            $_SESSION['error'] = __('Faila_Altera');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_suco WHERE id_suco = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    header("Location: ?pntl=suku/view");
    exit;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Altera_Suku') ?></h6>
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
                                $selected = ($row['id_posto'] == $data['id_posto']) ? "selected" : "";
                                echo "<option value='".$row['id_posto']."' ".$selected.">".htmlspecialchars($row['naran_posto'])." (".htmlspecialchars($row['naran_munisipio']).")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="naran_suco" class="form-label"><?= __('Naran_Suku') ?></label>
                        <input type="text" class="form-control" id="naran_suco" name="naran_suco" value="<?php echo htmlspecialchars($data['naran_suco']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> <?= __('Altera_Dadus') ?></button>
                    <a href="?pntl=suku/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>