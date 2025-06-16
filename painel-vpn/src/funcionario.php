<?php
session_start();
require_once 'db.php';

// Verifica se está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$nomeUsuario = htmlspecialchars($_SESSION['username']);
$certPath = "/opt/vpn-cert-generator/certs/{$nomeUsuario}_cert.zip";
$certDisponivel = file_exists($certPath);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel do Funcionário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn.red {
            background-color: #dc3545;
        }
        .btn.logout {
            margin-top: 30px;
            background-color: #6c757d;
        }
        .btn.disabled {
            background-color: #999;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bem-vindo, <?= $nomeUsuario ?>!</h1>
        <p>Escolha uma das opções abaixo:</p>

        <a href="gerar_certificado.php" class="btn">Gerar Certificado</a>
        <a href="remover_certificado.php" class="btn red">Remover Certificado</a>
        <a href="logout.php" class="btn logout">Sair</a>
    </div>
</body>
</html>

