<?php
const DB_HOST = 'localhost';
const DB_NAME = 'BD';
const DB_USER = 'root';
const DB_PASSWORD = '';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));

function pdo(): PDO {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Garante que a coluna comporta o hash (60+ caracteres). Tabelas antigas tinham varchar(20),
    // que cortava o hash. Roda uma vez por sessão.
    if (empty($_SESSION['schema_ok'])) {
        $pdo->exec('ALTER TABLE usuarios MODIFY senha VARCHAR(255) NOT NULL');
        $_SESSION['schema_ok'] = true;
    }
    return $pdo;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
