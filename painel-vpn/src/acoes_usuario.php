<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit;
}

$acao = $_POST['acao'] ?? '';
$usuariosSelecionados = $_POST['usuarios'] ?? [];

if (!is_array($usuariosSelecionados) || count($usuariosSelecionados) === 0) {
    $_SESSION['msg_erro'] = "Nenhum usuário selecionado.";
    header("Location: admin.php");
    exit;
}

// Validar IDs - aceitar só inteiros
$ids = array_filter($usuariosSelecionados, fn($id) => filter_var($id, FILTER_VALIDATE_INT));
if (empty($ids)) {
    $_SESSION['msg_erro'] = "IDs inválidos.";
    header("Location: admin.php");
    exit;
}

try {
    $pdo = getPDO();

    if ($acao === 'excluir') {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id IN ($in)");
        $stmt->execute($ids);
        $_SESSION['msg_sucesso'] = count($ids) . " usuário(s) excluído(s).";
        header("Location: admin.php");
        exit;
    }

    if ($acao === 'admin') {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE usuarios SET role = 'admin' WHERE id IN ($in)");
        $stmt->execute($ids);
        $_SESSION['msg_sucesso'] = count($ids) . " usuário(s) tornados Admin.";
        header("Location: admin.php");
        exit;
    }

    if ($acao === 'funcionario') {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("UPDATE usuarios SET role = 'funcionario' WHERE id IN ($in)");
        $stmt->execute($ids);
        $_SESSION['msg_sucesso'] = count($ids) . " usuário(s) atualizados para Funcionário.";
        header("Location: admin.php");
        exit;
    }

    if ($acao === 'desativar') {
        $idsParam = implode(',', $ids);
        header("Location: remover_certificado_admin.php?ids=" . urlencode($idsParam));
        exit;
    }

    if ($acao === 'ativar') {
        $idsParam = implode(',', $ids);
        header("Location: gerar_certificado_admin.php?ids=" . urlencode($idsParam));
        exit;
    }

    $_SESSION['msg_erro'] = "Ação inválida.";
    header("Location: admin.php");
    exit;

} catch (PDOException $e) {
    $_SESSION['msg_erro'] = "Erro no banco: " . $e->getMessage();
    header("Location: admin.php");
    exit;
}

