<?php
session_start();
require_once 'admin/koneksaun.php';

// Handle language for responses
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'tet';
$lang_file = "lang/{$current_lang}.json";
$lang_data = file_exists($lang_file) ? json_decode(file_get_contents($lang_file), true) : [];

function __($key) {
    global $lang_data;
    return isset($lang_data[$key]) ? $lang_data[$key] : $key;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || !empty($_POST)) {
    // Check connection
    if (!$conn) {
        echo json_encode(['status' => 'error', 'message' => 'Erro iha koneksaun base de dadus!']);
        exit();
    }

    $naran = mysqli_real_escape_string($conn, $_POST['naran']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $asuntu = mysqli_real_escape_string($conn, $_POST['asuntu']);
    $mensajen = mysqli_real_escape_string($conn, $_POST['mensajen']);
    $data_manda = date('Y-m-d H:i:s');

    // Create table if not exists (Ensuring robust table creation)
    $create_table = "CREATE TABLE IF NOT EXISTS tb_mensajen (
        id_mensajen INT AUTO_INCREMENT PRIMARY KEY,
        naran VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        asuntu VARCHAR(255) NOT NULL,
        mensajen TEXT NOT NULL,
        data_manda DATETIME NOT NULL,
        status ENUM('Unread', 'Read') DEFAULT 'Unread'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $create_table);

    $sql = "INSERT INTO tb_mensajen (naran, email, asuntu, mensajen, data_manda) VALUES ('$naran', '$email', '$asuntu', '$mensajen', '$data_manda')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => __('Susesu_Manda')]);
    } else {
        $db_error = mysqli_error($conn);
        echo json_encode(['status' => 'error', 'message' => __('Faila_Manda') . " ($db_error)"]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method: ' . $_SERVER['REQUEST_METHOD']]);
}
