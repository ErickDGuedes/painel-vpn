<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = escapeshellarg($_SESSION['username']); // segurança contra injeção
$comando = "sudo /opt/vpn-cert-generator/gerar_certificado.py $username 2>&1";
exec($comando, $saida, $retorno);

if (file_exists($zipFile)) {
    // Define permissões seguras: leitura apenas para o dono
    chmod($zipFile, 0400);
}

if ($retorno === 0) {
    $mensagem = "Certificado criado com sucesso " . htmlspecialchars($_SESSION['usuario']);
    $sucesso = true;
} else {
    $mensagem = "Erro ao criar certificado<br>" . implode("<br>", $saida);
    $sucesso = false;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal {
            display: block;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 400px;
            animation: fadeIn 0.3s ease;
        }

        .modal h2 {
            color: <?= $sucesso ? "#28a745" : "#dc3545" ?>;
        }

        .modal p {
            margin-top: 15px;
        }

        .btn {
            margin-top: 25px;
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="modal">
        <h2><?= $sucesso ? "Sucesso!" : "Erro" ?></h2>
        <p><?= $mensagem ?></p>
        <button class="btn" onclick="window.location.href='dashboard.php'">Voltar ao Painel</button>
    </div>
</body>
</html>
