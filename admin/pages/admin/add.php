<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran = htmlspecialchars(trim($_POST['naran_konpletu']));
    $user = htmlspecialchars(trim($_POST['username']));
    $pass = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    $level = $_POST['level'];

    if (!empty($naran) && !empty($user) && !empty($_POST['password']) && !empty($level)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tb_admin (naran_konpletu, username, password, level) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $naran, $user, $pass, $level);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = __('Susesu_Hatama_Admin');
            echo "<script>window.location='?pntl=admin/view';</script>";
        } else {
            $_SESSION['error'] = __('Faila_Hatama_Admin');
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['warning'] = __('Favor_Prenxe_Hotu');
    }
}
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4 text-white"><i class="fas fa-plus me-2 text-primary"></i><?= __('Form_Aumenta_Admin') ?></h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?= __('Naran_Kompletu') ?></label>
                        <input type="text" name="naran_konpletu" class="form-control" placeholder="Ex: Joana Maria" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Username') ?></label>
                        <input type="text" name="username" class="form-control" placeholder="Ex: joana123" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Password') ?></label>
                        <input type="password" name="password" class="form-control" placeholder="******" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?= __('Level') ?></label>
                        <select name="level" class="form-select bg-dark text-white border-0" required>
                            <option value="">-- <?= __('Hili_Level') ?> --</option>
                            <option value="administrator">Administrator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?= __('Rai_Dadus') ?></button>
                    <a href="?pntl=admin/view" class="btn btn-danger"><i class="fas fa-times me-2"></i><?= __('Kansela') ?></a>
                </form>
            </div>
        </div>
    </div>
</div>