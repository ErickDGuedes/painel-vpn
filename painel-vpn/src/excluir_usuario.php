<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$pdo = getPDO();

// Se vier a confirmação e IDs em array (exclusão múltipla)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_map('intval', $_POST['ids']);
    if (count($ids) > 0) {
        try {
            // Usa placeholders para cada ID
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id IN ($placeholders)");
            $stmt->execute($ids);

            header("Location: admin.php");
            exit;
        } catch (PDOException $e) {
            die("Erro ao excluir: " . $e->getMessage());
        }
    }
}

// Se for GET e tiver um id único (exclusão simples)
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    // Buscar dados para exibir
    $stmt = $pdo->prepare("SELECT username FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Usuário não encontrado.";
        exit;
    }
    // Exibe formulário para exclusão de um único usuário
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <title>Confirmar Exclusão</title>
        <style>
            body { font-family: Arial; padding: 40px; background: #f4f4f4; text-align: center; }
            .box { background: white; padding: 30px; border-radius: 10px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
            button { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 10px; }
            .btn-red { background: #dc3545; color: white; }
            .btn-grey { background: #6c757d; color: white; }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>Excluir usuário: <?= htmlspecialchars($user['username']) ?>?</h2>
            <form method="post">
                <input type="hidden" name="ids[]" value="<?= $id ?>">
                <button type="submit" name="confirm" class="btn-red">Sim, excluir</button>
                <a href="admin.php"><button type="button" class="btn-grey">Cancelar</button></a>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Se não tiver id nem ids no POST, erro
echo "ID do usuário não especificado.";
exit;

