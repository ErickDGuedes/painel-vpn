<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Usuário não logado, redireciona para login
    header("Location: login.php");
    exit;
}

// Redireciona baseado no role
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit;
} else {
    // Usuário comum
    header("Location: funcionario.php");
    exit;
}

