<?php
// Foti ID husi URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    $_SESSION['error'] = __('ID_La_Validu');
    echo "<script>window.location='?pntl=munisipio/view';</script>";
    exit;
}

// Prosesa update dadus
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran_munisipio = htmlspecialchars(trim($_POST['naran_munisipio']));

    if (!empty($naran_munisipio)) {
        // Uza prepared statement
        $stmt = mysqli_prepare($conn, "UPDATE tb_munisipio SET naran_munisipio = ? WHERE id_munisipio = ?");
        mysqli_stmt_bind_param($stmt, "si", $naran_munisipio, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Altera');
            echo "<script>window.location='?pntl=munisipio/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Altera');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Munisipiu');
    }
}

// Foti dadus tuan husi database
$stmt = mysqli_prepare($conn, "SELECT * FROM tb_munisipio WHERE id_munisipio = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = __('Dadus_La_Eziste');
    echo "<script>window.location='?pntl=munisipio/view';</script>";
    exit;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6 mx-auto">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4"><?= __('Form_Altera_Munisipiu') ?></h6>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="naran_munisipio" class="form-label"><?= __('Naran_Munisipiu') ?></label>
                        <input type="text" class="form-control" id="naran_munisipio" name="naran_munisipio" value="<?php echo htmlspecialchars($data['naran_munisipio']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> <?= __('Altera_Dadus') ?></button>
                    <a href="?pntl=munisipio/view" class="btn btn-danger"><i class="fas fa-times"></i> <?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>