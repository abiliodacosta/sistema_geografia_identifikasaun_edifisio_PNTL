<?php
session_start();
require_once 'koneksaun.php';

// Handle language selection
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['tet', 'pt', 'en'])) {
        $_SESSION['lang'] = $lang;
    }
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tet';

$lang_file = "../lang/{$current_lang}.json";
if (file_exists($lang_file)) {
    $lang_data = json_decode(file_get_contents($lang_file), true);
} else {
    $lang_data = [];
}

function __($key) {
    global $lang_data;
    return isset($lang_data[$key]) ? $lang_data[$key] : $key;
}
if (isset($_SESSION['username'])) {
    header("Location: home");
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM tb_admin WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password']) || $row['password'] == md5($password)) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['naran_konpletu'] = $row['naran_konpletu'];
            $_SESSION['level'] = $row['level'];
            header("Location: home");
            exit();
        } else {
            $error = __('Login_Error');
        }
    } else {
        $error = __('Login_Error');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PNTL Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="img/pntl.png" rel="icon">
    <style>
        :root {
            --pntl-blue: #002366;
            --pntl-red: #CE1126;
            --pntl-gold: #FFCC00;
            --pntl-dark: #0a1931;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--pntl-dark) 0%, var(--pntl-blue) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #fff;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 40px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            animation: fadeIn 0.8s ease;
            margin: auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            transition: transform 0.3s ease;
        }

        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .login-header h2 {
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff !important;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--pntl-gold);
            box-shadow: 0 0 0 0.25rem rgba(255, 204, 0, 0.1);
        }

        .btn-login {
            background: var(--pntl-gold);
            color: var(--pntl-blue);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-login:hover, .btn-login:focus, .btn-login:active {
            background: #fff !important;
            color: var(--pntl-blue) !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 204, 0, 0.2);
            outline: none;
        }

        .alert-error {
            background: rgba(206, 17, 38, 0.15);
            border: 1px solid var(--pntl-red);
            color: #ffbaba;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.85rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-return {
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-return:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.4);
        }

        /* Responsive ajustmentu ba mobile */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
                width: 92%;
            }
            .logo-container {
                width: 90px;
                height: 90px;
            }
            .login-header h2 {
                font-size: 1.2rem;
            }
            .login-header p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="logo-container">
                <img src="img/pntl.png" alt="PNTL Logo">
            </div>
            <h2><?= __('PNTL_Naran') ?> Admin</h2>
            <p><?= __('Sistema_Title') ?></p>
        </div>

        <?php if ($error): ?>
            <div class="alert-error">
                <i class="fa fa-exclamation-circle me-2"></i><?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label"><?= __('Username') ?></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fa fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="<?= __('Username_Placeholder') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= __('Password') ?></label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50"><i class="fa fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="<?= __('Password_Placeholder') ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login"><?= __('Login_Btn') ?></button>
            <a href="../" class="btn-return">
                <i class="fa fa-arrow-left me-2"></i> <?= __('Fila_Vizitante') ?>
            </a>
        </form>

        <div class="text-center mt-4">
            <small class="text-white-50">&copy; 2026 PNTL Geographic System</small>
        </div>
    </div>

</body>
</html>