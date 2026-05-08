<?php
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
        // Valida se iha ajenda ne'ebé hanesan iha tempu hanesan
        $cek_query = mysqli_prepare($conn, "SELECT id_ajenda FROM tb_ajenda WHERE data_ajenda = ? AND ora_ajenda = ?");
        mysqli_stmt_bind_param($cek_query, "ss", $data_ajenda, $ora_ajenda);
        mysqli_stmt_execute($cek_query);
        mysqli_stmt_store_result($cek_query);
        
        if (mysqli_stmt_num_rows($cek_query) > 0) {
            $_SESSION['warning'] = __('Ajenda_Eziste');
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO tb_ajenda (titulu, titulu_pt, titulu_en, data_ajenda, ora_ajenda, diskrisaun, diskrisaun_pt, diskrisaun_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssssss", $titulu, $titulu_pt, $titulu_en, $data_ajenda, $ora_ajenda, $diskrisaun, $diskrisaun_pt, $diskrisaun_en);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = __('Susesu_Hatama_Ajenda');
                echo "<script>window.location='?pntl=ajenda/view';</script>";
                exit;
            } else {
                $_SESSION['error'] = __('Faila_Hatama_Ajenda');
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($cek_query);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-10 mx-auto">
            <div class="widget-card">
                <h4 class="mb-4 text-white text-center"><?= __('Form_Aumenta_Ajenda') ?></h4>
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="titulu" class="form-label"><?= __('Titulu_Ajenda') ?> (TET)</label>
                            <input type="text" class="form-control" id="titulu" name="titulu" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="titulu_pt" class="form-label"><?= __('Titulu_Ajenda') ?> (PT)</label>
                            <input type="text" class="form-control" id="titulu_pt" name="titulu_pt">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="titulu_en" class="form-label"><?= __('Titulu_Ajenda') ?> (EN)</label>
                            <input type="text" class="form-control" id="titulu_en" name="titulu_en">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_ajenda" class="form-label"><?= __('Data_Enkontru') ?></label>
                            <input type="date" class="form-control bg-white text-dark" id="data_ajenda" name="data_ajenda" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ora_ajenda" class="form-label"><?= __('Oras_Enkontru') ?></label>
                            <input type="time" class="form-control bg-white text-dark" id="ora_ajenda" name="ora_ajenda" required>
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
                    <a href="?pntl=ajenda/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
