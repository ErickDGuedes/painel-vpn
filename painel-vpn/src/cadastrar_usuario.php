<?php
require_once 'db.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $role = $_POST['role'] ?? 'funcionario';

    if (empty($username) || empty($email) || empty($senha)) {
        $mensagem = "Por favor, preencha todos os campos.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, senha_hash, email, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $senha_hash, $email, $role]);
            $mensagem = "✅ Usuário cadastrado com sucesso!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem = "⚠️ Erro: Username ou e-mail já cadastrado.";
            } else {
                $mensagem = "Erro ao cadastrar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            max-width: 600px;
        }
        h1 {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 12px;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .mensagem {
            margin-bottom: 20px;
            padding: 10px;
            border-left: 4px solid #007bff;
            background: #f0f8ff;
        }
        .link-voltar {
            margin-top: 20px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
        }
        .link-voltar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Cadastrar Novo Usuário</h1>

<?php if (!empty($mensagem)): ?>
    <div class="mensagem"><?= htmlspecialchars($mensagem) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>Usuário:
        <input type="text" name="username" required>
    </label>

    <label>E-mail:
        <input type="email" name="email" required>
    </label>

    <label>Senha:
        <input type="password" name="senha" required>
    </label>

    <label>Perfil:
        <select name="role">
            <option value="funcionario">Funcionário</option>
            <option value="admin">Administrador</option>
        </select>
    </label>

    <button type="submit" class="btn">Cadastrar</button>
</form>

<a href="admin.php" class="link-voltar">← Voltar para Painel Admin</a>

</body>
</html>
