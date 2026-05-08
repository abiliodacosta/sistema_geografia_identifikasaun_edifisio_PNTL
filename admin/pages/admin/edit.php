<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran = htmlspecialchars(trim($_POST['naran_konpletu']));
    $user = htmlspecialchars(trim($_POST['username']));
    $pass = trim($_POST['password']);

    $level = $_POST['level'];

    if (!empty($pass)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE tb_admin SET naran_konpletu = ?, username = ?, password = ?, level = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssi", $naran, $user, $hashed_pass, $level, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE tb_admin SET naran_konpletu = ?, username = ?, level = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $naran, $user, $level, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = __('Susesu_Altera_Admin');
        echo "<script>window.location='?pntl=admin/view';</script>";
        exit;
    } else {
        $_SESSION['error'] = __('Faila_Altera_Admin');
    }
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_prepare($conn, "SELECT * FROM tb_admin WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4 text-white"><i class="fas fa-edit me-2 text-primary"></i><?= __('Form_Altera_Admin') ?></h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= __('Naran_Kompletu') ?></label>
                        <input type="text" name="naran_konpletu" class="form-control" value="<?php echo htmlspecialchars($data['naran_konpletu']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Username') ?></label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Password_Foun_Info') ?></label>
                        <input type="password" name="password" class="form-control" placeholder="******">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Level') ?></label>
                        <select name="level" class="form-select bg-dark text-white border-0" required>
                            <option value="administrator" <?php echo $data['level'] == 'administrator' ? 'selected' : ''; ?>>Administrator</option>
                            <option value="admin" <?php echo $data['level'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-edit me-2"></i><?= __('Altera_Dadus') ?></button>
                    <a href="?pntl=admin/view" class="btn btn-danger"><i class="fas fa-times me-2"></i><?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
