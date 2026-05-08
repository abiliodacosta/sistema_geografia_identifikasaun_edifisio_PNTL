<?php

// auth.php - Faile para verifika login
session_start();

// Verifika se utilizador seidauk login
function checkLogin() {
    if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
        // Se seidauk login, redirect ba login.php
        header("Location: index.php");
        exit();
    }
}

// Verifika se mak admin (opsional)
function checkAdmin() {
    checkLogin();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        header("Location: home.php"); // ou pagina aat
        exit();
    }
}
?>