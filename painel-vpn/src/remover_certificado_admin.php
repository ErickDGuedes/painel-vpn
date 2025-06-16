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
    $_SESSION['msg_erro'] = "Nenhum usuário selecionado para remover certificado.";
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
    foreach ($usuarios as $username) {
        $file = "/opt/vpn-cert-generator/certs/" . escapeshellarg($username) . "_cert.zip";
        // Use escapeshellarg para evitar injeção de comando
        $comando = "sudo rm /opt/vpn-cert-generator/certs/" . escapeshellarg($username . "_cert.zip") . " 2>&1";
        exec($comando, $saida, $retorno);
        if ($retorno !== 0) {
            $erros[] = "Erro ao remover certificado de $username: " . implode(" ", $saida);
        }
    }

    if (empty($erros)) {
        $_SESSION['msg_sucesso'] = count($usuarios) . " certificado(s) removido(s) com sucesso.";
    } else {
        $_SESSION['msg_erro'] = implode("<br>", $erros);
    }
    header("Location: admin.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['msg_erro'] = "Erro ao acessar banco: " . $e->getMessage();
    header("Location: admin.php");
    exit;
}

