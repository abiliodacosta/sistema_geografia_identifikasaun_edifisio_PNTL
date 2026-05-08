<?php
/**
 * PNTL Dynamic Mail Config
 * Reads SMTP settings from the database (tb_smtp_config).
 * Configure credentials via: Admin → Settings → Email SMTP
 */

require_once __DIR__ . '/koneksaun.php';

$_smtp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_smtp_config WHERE id=1 LIMIT 1"));

define('MAIL_SMTP_HOST',   $_smtp['smtp_host']      ?? 'smtp.gmail.com');
define('MAIL_SMTP_PORT',   (int)($_smtp['smtp_port'] ?? 587));
define('MAIL_SMTP_SECURE', $_smtp['smtp_secure']    ?? 'tls');
define('MAIL_FROM_EMAIL',  $_smtp['smtp_user']      ?? '');
define('MAIL_FROM_NAME',   $_smtp['smtp_from_name'] ?? 'PNTL Admin');
define('MAIL_PASSWORD',    $_smtp['smtp_pass']      ?? '');
