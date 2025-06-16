<?php
function getPDO() {
    $host = 'localhost';
    $dbname = 'vpn_system';
    $user = 'vpnuser';
    $pass = '123456';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    try {
        $pdo = new PDO($dsn, $user, $pass);
        // configura o PDO para lançar exceções em erros
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}
?>
