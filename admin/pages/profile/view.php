<?php
$id_admin = $_SESSION['id'];

// Prosesa Update Profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naran = htmlspecialchars(trim($_POST['naran_konpletu']));
    $user = htmlspecialchars(trim($_POST['username']));
    $pass = trim($_POST['password']);

    if (!empty($pass)) {
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE tb_admin SET naran_konpletu = ?, username = ?, password = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $naran, $user, $hashed_pass, $id_admin);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE tb_admin SET naran_konpletu = ?, username = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $naran, $user, $id_admin);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['username'] = $user;
        $_SESSION['naran_konpletu'] = $naran;
        echo "<script>alert('Perfil atualiza ona!'); window.location='?pntl=profile/view';</script>";
    } else {
        echo "<script>alert('Faila atu atualiza perfil!');</script>";
    }
    mysqli_stmt_close($stmt);
}

// Foti dadus admin
$stmt = mysqli_prepare($conn, "SELECT * FROM tb_admin WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_admin);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4 text-white"><i class="fa fa-user-circle me-2 text-primary"></i>Perfil de Utilizador</h4>
                <form method="POST">
                    <div class="mb-3 text-center">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                            <i class="fa fa-user text-white fa-4x"></i>
                        </div>
                        <h5 class="text-white"><?php echo htmlspecialchars($admin['naran_konpletu']); ?></h5>
                        <p class="text-muted">Administrator</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Naran Kompletu</label>
                        <input type="text" name="naran_konpletu" class="form-control" value="<?php echo htmlspecialchars($admin['naran_konpletu']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Foun (husik mamuk se lakohi troka)</label>
                        <input type="password" name="password" class="form-control" placeholder="******">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-2">
                        <i class="fa fa-save me-2"></i> Rai Mudansa
                    </button>
                </form>
            </div>
        </div>
        
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h4 class="mb-4 text-white"><i class="fa fa-shield-alt me-2 text-primary"></i>Informasaun Seguransa</h4>
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent text-white-50 border-bottom border-dark py-3">
                        <i class="fa fa-check-circle text-success me-2"></i> Sessun ativa hela
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 border-bottom border-dark py-3">
                        <i class="fa fa-lock text-warning me-2"></i> Password uza kódigu enkripsaun hashing foun.
                    </li>
                    <li class="list-group-item bg-transparent text-white-50 py-3">
                        <i class="fa fa-info-circle text-info me-2"></i> Labele fahe ita-boot nia password ba ema seluk.
                    </li>
                </ul>
                <div class="alert alert-info mt-4 border-0 bg-dark text-white-50">
                    <small>Se ita-boot troka <strong>Username</strong>, ita-boot presiza uza username foun ne'e iha login tuir mai.</small>
                </div>
            </div>
        </div>
    </div>
</div>
