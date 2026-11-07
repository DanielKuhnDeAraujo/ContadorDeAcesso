<?php
require __DIR__ . '/config.php';
// Só sai via POST com token CSRF. O logout não aumenta o contador:
// a flag "sem_contar" faz a próxima exibição da página principal não contar.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    unset($_SESSION['usuario_id'], $_SESSION['usuario_nome']);
    $_SESSION['sem_contar'] = true;
    session_regenerate_id(true);
}
header('Location: index.php');
exit;
