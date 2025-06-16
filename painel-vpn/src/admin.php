<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT id, username, email, role FROM usuarios ORDER BY username ASC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Painel Admin - Gerenciar Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { cursor: pointer; }
        .btn {
            padding: 8px 12px;
            margin: 4px 5px;
            font-size: 14px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn.red { background: #dc3545; }
        .btn.gray { background: #6c757d; }
        .btn:hover { opacity: 0.9; }
        .top-bar { display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center; flex-wrap: wrap; }
        #filtro { padding: 7px; width: 250px; }
        .action-bar { margin-top: 10px; margin-bottom: 10px; }
        /* Mensagens */
        .msg-sucesso {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
        }
        .msg-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<h1>Painel Admin - Gerenciar Usuários</h1>

<?php
if (!empty($_SESSION['msg_sucesso'])) {
    echo '<div class="msg-sucesso">' . $_SESSION['msg_sucesso'] . '</div>';
    unset($_SESSION['msg_sucesso']);
}
if (!empty($_SESSION['msg_erro'])) {
    echo '<div class="msg-erro">' . $_SESSION['msg_erro'] . '</div>';
    unset($_SESSION['msg_erro']);
}
?>

<form method="post" action="acoes_usuario.php" id="formUsuarios">
    <div class="top-bar">
        <input type="text" id="filtro" placeholder="Buscar por nome..." onkeydown="filtrarTabela(event)" autocomplete="off" />
        <div class="action-bar">
            <button type="button" class="btn" onclick="executarAcao('admin')">Tornar Admin</button>
            <button type="button" class="btn red" onclick="executarAcao('funcionario')">Remover Admin</button>
            <button type="button" class="btn red" onclick="executarAcao('desativar')">Desativar</button>
            <button type="button" class="btn" onclick="executarAcao('ativar')">Ativar</button>
            <button type="button" class="btn red" onclick="executarAcao('excluir')">Excluir</button>
            <button type="button" class="btn" onclick="window.location.href='cadastrar_usuario.php'">+ Novo Usuário</button>
        </div>
    </div>
    <input type="hidden" name="acao" id="acaoEscolhida" value="">

    <table id="tabelaUsuarios">
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                <th onclick="ordenarTabela(1)">Nome &#x25B2;&#x25BC;</th>
                <th onclick="ordenarTabela(2)">Email &#x25B2;&#x25BC;</th>
                <th onclick="ordenarTabela(3)">Perfil &#x25B2;&#x25BC;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $user): ?>
            <tr>
                <td><input type="checkbox" name="usuarios[]" value="<?= htmlspecialchars($user['id']) ?>"></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<form method="post" action="logout.php" style="margin-top: 20px;">
    <button type="submit" class="btn red">Sair</button>
</form>

<script>
function filtrarTabela(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // evita enviar form
        const filtro = document.getElementById('filtro').value.toLowerCase();
        const linhas = document.querySelectorAll('#tabelaUsuarios tbody tr');
        linhas.forEach(linha => {
            const nome = linha.cells[1].textContent.toLowerCase();
            linha.style.display = nome.includes(filtro) ? '' : 'none';
        });
    }
}

let ordemAsc = true;
function ordenarTabela(colIndex) {
    const tabela = document.getElementById('tabelaUsuarios');
    const tbody = tabela.tBodies[0];
    const linhas = Array.from(tbody.rows);

    linhas.sort((a, b) => {
        const valA = a.cells[colIndex].textContent.toLowerCase();
        const valB = b.cells[colIndex].textContent.toLowerCase();
        return ordemAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
    });

    ordemAsc = !ordemAsc;
    linhas.forEach(linha => tbody.appendChild(linha));
}

function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="usuarios[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

function executarAcao(acao) {
    const checkboxes = document.querySelectorAll('input[name="usuarios[]"]:checked');
    if (checkboxes.length === 0) {
        alert('Selecione pelo menos um usuário.');
        return;
    }
    if (acao === 'excluir' && !confirm('Tem certeza que deseja excluir os usuários selecionados?')) return;
    document.getElementById('acaoEscolhida').value = acao;
    document.getElementById('formUsuarios').submit();
}
</script>

</body>
</html>
