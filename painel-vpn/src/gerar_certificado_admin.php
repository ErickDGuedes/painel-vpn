<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$idsParam = $_GET['ids'] ?? '';
$ids = array_filter(explode(',', $idsParam), fn($id) => filter_var($id, FILTER_VALIDATE_INT));

if (empty($ids)) {
    $_SESSION['msg_erro'] = "Nenhum usuário selecionado para gerar certificado.";
    header("Location: admin.php");
    exit;
}

try {
    $pdo = getPDO();

    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id IN ($in)");
    $stmt->execute($ids);
    $usuarios = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $erros = [];
    $sucessoCount = 0;

    foreach ($usuarios as $username) {
        $userArg = escapeshellarg($username);
        $comando = "sudo /opt/vpn-cert-generator/gerar_certificado.py $userArg 2>&1";
        exec($comando, $saida, $retorno);

        if ($retorno === 0) {
            $sucessoCount++;
            $zipFile = "/opt/vpn-cert-generator/certs/{$username}_cert.zip";
            if (file_exists($zipFile)) {
                chmod($zipFile, 0400);
            }
        } else {
            $erros[] = "Erro ao gerar certificado para $username: " . implode(" ", $saida);
        }
    }
    if ($sucessoCount > 0) {
        $_SESSION['msg_sucesso'] = "$sucessoCount certificado(s) gerado(s) com sucesso.";
        if (!empty($erros)) {
            $_SESSION['msg_erro'] = implode("<br>", $erros);
        }
    } else {
        $_SESSION['msg_erro'] = "Nenhum certificado foi gerado.<br>" . implode("<br>", $erros);
    }

    header("Location: admin.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['msg_erro'] = "Erro ao acessar banco: " . $e->getMessage();
    header("Location: admin.php");
    exit;
}

