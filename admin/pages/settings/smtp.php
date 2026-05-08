<?php
require_once 'auth.php';
checkLogin();

$success = '';
$error   = '';

// Save SMTP settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_smtp'])) {
    $host      = mysqli_real_escape_string($conn, trim($_POST['smtp_host']));
    $port      = (int)$_POST['smtp_port'];
    $secure    = in_array($_POST['smtp_secure'], ['tls','ssl']) ? $_POST['smtp_secure'] : 'tls';
    $user      = mysqli_real_escape_string($conn, trim($_POST['smtp_user']));
    $from_name = mysqli_real_escape_string($conn, trim($_POST['smtp_from_name']));
    $pass_raw  = trim($_POST['smtp_pass']);

    if (!empty($pass_raw)) {
        $pass = mysqli_real_escape_string($conn, $pass_raw);
        $sql  = "UPDATE tb_smtp_config SET smtp_host='$host', smtp_port=$port, smtp_secure='$secure', smtp_user='$user', smtp_pass='$pass', smtp_from_name='$from_name' WHERE id=1";
    } else {
        $sql = "UPDATE tb_smtp_config SET smtp_host='$host', smtp_port=$port, smtp_secure='$secure', smtp_user='$user', smtp_from_name='$from_name' WHERE id=1";
    }

    if (mysqli_query($conn, $sql)) {
        $success = "Konfigurasaun email salva ho susesu!";
    } else {
        $error = "Erro: " . mysqli_error($conn);
    }
}

// Test Connection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_smtp'])) {
    $phpmailer_path = __DIR__ . '/../../lib/PHPMailer-master/src/';
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'SMTP.php';

    // Get current data from POST to test before saving
    $t_host   = trim($_POST['smtp_host']);
    $t_port   = (int)$_POST['smtp_port'];
    $t_secure = $_POST['smtp_secure'];
    $t_user   = trim($_POST['smtp_user']);
    $t_from   = trim($_POST['smtp_from_name']);
    $t_pass   = trim($_POST['smtp_pass']);
    
    // If pass is empty, use the one from DB
    if (empty($t_pass)) {
        $db_cfg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT smtp_pass FROM tb_smtp_config WHERE id=1"));
        $t_pass = $db_cfg['smtp_pass'];
    }

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $t_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $t_user;
        $mail->Password   = $t_pass;
        $mail->SMTPSecure = $t_secure;
        $mail->Port       = $t_port;
        
        // Bypass SSL certificate verification for better compatibility
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->setFrom($t_user, $t_from);
        $mail->addAddress($t_user); // Send to self
        $mail->isHTML(true);
        $mail->Subject = 'PNTL SMTP Test Connection';
        $mail->Body    = 'Se ita simu email ne\'e, katak konfigurasaun SMTP servisu ho susesu!<br><br><b>PNTL Admin System</b>';
        $mail->send();
        $success = "Koneksaun Susesu! Email teste haruka ona ba <b>$t_user</b>.";
    } catch (\Exception $e) {
        $err = isset($mail) ? $mail->ErrorInfo : $e->getMessage();
        $error = "Teste Faila: " . htmlspecialchars($err);
        
        if (strpos($err, 'Could not authenticate') !== false) {
            $error .= "<br><br><small><b>Dika:</b> Erro ne'e baibain tanba Password sala. Favor uza <b>App Password</b> (16 karakter) husi Google, la'os password login Gmail baibain.</small>";
        } elseif (strpos($err, '10060') !== false) {
            $error .= "<br><br><small><b>Dika:</b> Erro 10060 (Timeout) baibain tanba ISP blokia portu 587 ka 465. Tenta troka Portu ka uza rede internet seluk.</small>";
        }
    }
}

// Load current settings
$cfg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_smtp_config WHERE id=1"));
?>

<div class="container-fluid pt-4 px-4">
    <div class="widget-card">
        <div class="d-flex align-items-center mb-4 gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:46px;height:46px;background:linear-gradient(135deg,#002366,#0044aa);">
                <i class="fa fa-cog" style="color:#FFCC00;font-size:1.1rem;"></i>
            </div>
            <div>
                <h5 class="mb-0 text-white fw-bold">Konfigurasaun Email SMTP</h5>
                <small class="text-white-50">Halo email reply servisu husi website laran</small>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fa fa-check-circle me-2"></i> <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4">
            <i class="fa fa-exclamation-circle me-2"></i> <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- HOW-TO Banner -->
        <div class="rounded-3 p-3 mb-4 d-flex align-items-start gap-3"
             style="background:rgba(255,204,0,0.08);border:1px solid rgba(255,204,0,0.2);">
            <i class="fa fa-info-circle mt-1" style="color:#FFCC00;font-size:1.2rem;"></i>
            <div>
                <p class="mb-1 text-white fw-semibold">Oinsá konfigura Gmail SMTP:</p>
                <ol class="text-white-50 small mb-0 ps-3">
                    <li>Ba <strong class="text-white">myaccount.google.com</strong> → Security → 2-Step Verification → <strong class="text-white">Habilita</strong></li>
                    <li>Depois ba <strong class="text-white">App Passwords</strong> → Kria password foun ba "Mail"</li>
                    <li>Kopia password 16 karakter ne'e (ex: <code style="color:#FFCC00;">abcd efgh ijkl mnop</code>) mai iha iha okos</li>
                    <li>Klik <strong class="text-white">Salva</strong> — email reply bele servisu tiha ona!</li>
                </ol>
            </div>
        </div>

        <form method="POST" action="">
            <div class="row g-4">
                <!-- SMTP Host -->
                <div class="col-md-6">
                    <label class="text-white-50 small mb-2">SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control smtp-input"
                           value="<?= htmlspecialchars($cfg['smtp_host']) ?>"
                           placeholder="smtp.gmail.com">
                    <small class="text-white-50">Gmail: smtp.gmail.com</small>
                </div>

                <!-- SMTP Port -->
                <div class="col-md-3">
                    <label class="text-white-50 small mb-2">Port</label>
                    <input type="number" name="smtp_port" class="form-control smtp-input"
                           value="<?= $cfg['smtp_port'] ?>" placeholder="587">
                    <small class="text-white-50">TLS: 587 / SSL: 465</small>
                </div>

                <!-- Secure -->
                <div class="col-md-3">
                    <label class="text-white-50 small mb-2">Enkriptasaun</label>
                    <select name="smtp_secure" class="form-select smtp-input">
                        <option value="tls" <?= $cfg['smtp_secure']=='tls'?'selected':'' ?>>TLS (Rekomendasaun)</option>
                        <option value="ssl" <?= $cfg['smtp_secure']=='ssl'?'selected':'' ?>>SSL</option>
                    </select>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="text-white-50 small mb-2">Gmail Adresu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text smtp-input-addon"><i class="fa fa-envelope"></i></span>
                        <input type="email" name="smtp_user" class="form-control smtp-input"
                               value="<?= htmlspecialchars($cfg['smtp_user']) ?>"
                               placeholder="yourname@gmail.com" required>
                    </div>
                </div>

                <!-- From Name -->
                <div class="col-md-6">
                    <label class="text-white-50 small mb-2">Naran Remetente</label>
                    <div class="input-group">
                        <span class="input-group-text smtp-input-addon"><i class="fa fa-user"></i></span>
                        <input type="text" name="smtp_from_name" class="form-control smtp-input"
                               value="<?= htmlspecialchars($cfg['smtp_from_name']) ?>"
                               placeholder="PNTL Admin">
                    </div>
                </div>

                <!-- App Password -->
                <div class="col-md-12">
                    <label class="text-white-50 small mb-2">
                        Gmail App Password <span class="text-danger">*</span>
                        <span class="badge ms-1" style="background:rgba(255,204,0,0.15);color:#FFCC00;font-size:0.7rem;">
                            16 karakter husi Google App Passwords
                        </span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text smtp-input-addon"><i class="fa fa-key"></i></span>
                        <input type="password" name="smtp_pass" id="smtpPass" class="form-control smtp-input"
                               placeholder="<?= !empty($cfg['smtp_pass']) ? '●●●● ●●●● ●●●● ●●●● (salva ona)' : 'xxxx xxxx xxxx xxxx' ?>">
                        <button class="btn smtp-input-addon" type="button" onclick="togglePass()" id="togglePassBtn">
                            <i class="fa fa-eye" id="passEyeIcon"></i>
                        </button>
                    </div>
                    <?php if (!empty($cfg['smtp_pass'])): ?>
                    <small class="text-success"><i class="fa fa-check-circle me-1"></i>Password salva ona. Laiha muda ne'e se labele muda.</small>
                    <?php else: ?>
                    <small class="text-warning"><i class="fa fa-exclamation-triangle me-1"></i>Password seidauk konfigurasaun.</small>
                    <?php endif; ?>
                </div>

                <!-- Test Connection Status -->
                <!-- Actions -->
                <div class="col-12 mt-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <button type="submit" name="save_smtp" class="btn fw-bold px-4 py-2"
                                style="background:linear-gradient(135deg,#FFCC00,#ff9800);color:#002366;border:none;border-radius:12px;box-shadow:0 4px 15px rgba(255,204,0,0.3);">
                            <i class="fa fa-save me-2"></i> Salva Konfigurasaun
                        </button>
                        
                        <button type="submit" name="test_smtp" class="btn btn-info fw-bold px-4 py-2 text-white" 
                                style="border-radius:12px; background: #0dcaf0; border:none;">
                            <i class="fa fa-paper-plane me-2"></i> Teste Koneksaun
                        </button>

                        <a href="?pntl=mensajen/view" class="btn btn-outline-light px-4 py-2" style="border-radius:12px;">
                            <i class="fa fa-arrow-left me-2"></i> Fila ba Mensajen
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.smtp-input, .smtp-input:focus {
    background: rgba(255,255,255,0.07) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    color: #fff !important;
    border-radius: 10px !important;
    padding: 10px 15px !important;
    transition: all 0.3s ease;
}
.smtp-input:focus {
    border-color: rgba(255,204,0,0.5) !important;
    box-shadow: 0 0 0 3px rgba(255,204,0,0.1) !important;
}
.smtp-input::placeholder { color: rgba(255,255,255,0.3) !important; }
.smtp-input-addon {
    background: rgba(255,255,255,0.07) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    color: rgba(255,255,255,0.5) !important;
    border-radius: 10px 0 0 10px !important;
}
.input-group .smtp-input { border-radius: 0 10px 10px 0 !important; }
.input-group .smtp-input-addon:last-child { border-radius: 0 10px 10px 0 !important; }
.form-select.smtp-input option { background: #0a1931; color: #fff; }
</style>

<script>
function togglePass() {
    const input = document.getElementById('smtpPass');
    const icon  = document.getElementById('passEyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
