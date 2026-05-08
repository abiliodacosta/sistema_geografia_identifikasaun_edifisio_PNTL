<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    echo "<script>window.location='?pntl=ajenda/view';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulu = htmlspecialchars(trim($_POST['titulu']));
    $titulu_pt = htmlspecialchars(trim($_POST['titulu_pt']));
    $titulu_en = htmlspecialchars(trim($_POST['titulu_en']));
    $data_ajenda = $_POST['data_ajenda'];
    $ora_ajenda = $_POST['ora_ajenda'];
    $diskrisaun = htmlspecialchars(trim($_POST['diskrisaun']));
    $diskrisaun_pt = htmlspecialchars(trim($_POST['diskrisaun_pt']));
    $diskrisaun_en = htmlspecialchars(trim($_POST['diskrisaun_en']));

    if (!empty($titulu) && !empty($data_ajenda) && !empty($ora_ajenda)) {
        // Valida se dupla ho ID seluk
        $cek_query = mysqli_prepare($conn, "SELECT id_ajenda FROM tb_ajenda WHERE data_ajenda = ? AND ora_ajenda = ? AND id_ajenda != ?");
        mysqli_stmt_bind_param($cek_query, "ssi", $data_ajenda, $ora_ajenda, $id);
        mysqli_stmt_execute($cek_query);
        mysqli_stmt_store_result($cek_query);
        
        if (mysqli_stmt_num_rows($cek_query) > 0) {
            $_SESSION['warning'] = __('Ajenda_Eziste');
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE tb_ajenda SET titulu = ?, titulu_pt = ?, titulu_en = ?, data_ajenda = ?, ora_ajenda = ?, diskrisaun = ?, diskrisaun_pt = ?, diskrisaun_en = ? WHERE id_ajenda = ?");
            mysqli_stmt_bind_param($stmt, "ssssssssi", $titulu, $titulu_pt, $titulu_en, $data_ajenda, $ora_ajenda, $diskrisaun, $diskrisaun_pt, $diskrisaun_en, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = __('Susesu_Altera_Ajenda');
                echo "<script>window.location='?pntl=ajenda/view';</script>";
                exit;
            } else {
                $_SESSION['error'] = __('Faila_Altera_Ajenda');
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek_query);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_ajenda WHERE id_ajenda = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    echo "<script>window.location='?pntl=ajenda/view';</script>";
    exit;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-10 mx-auto">
            <div class="widget-card">
                <h4 class="mb-4 text-white text-center"><?= __('Form_Altera_Ajenda') ?></h4>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="titulu" class="form-label"><?= __('Titulu_Ajenda') ?> (TET)</label>
                            <input type="text" class="form-control" id="titulu" name="titulu" value="<?php echo htmlspecialchars($data['titulu']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="titulu_pt" class="form-label"><?= __('Titulu_Ajenda') ?> (PT)</label>
                            <input type="text" class="form-control" id="titulu_pt" name="titulu_pt" value="<?php echo htmlspecialchars($data['titulu_pt']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="titulu_en" class="form-label"><?= __('Titulu_Ajenda') ?> (EN)</label>
                            <input type="text" class="form-control" id="titulu_en" name="titulu_en" value="<?php echo htmlspecialchars($data['titulu_en']); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_ajenda" class="form-label"><?= __('Data_Enkontru') ?></label>
                            <input type="date" class="form-control bg-white text-dark" id="data_ajenda" name="data_ajenda" value="<?php echo htmlspecialchars($data['data_ajenda']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ora_ajenda" class="form-label"><?= __('Oras_Enkontru') ?></label>
                            <input type="time" class="form-control bg-white text-dark" id="ora_ajenda" name="ora_ajenda" value="<?php echo htmlspecialchars($data['ora_ajenda']); ?>" required>
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
                    <a href="?pntl=ajenda/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
