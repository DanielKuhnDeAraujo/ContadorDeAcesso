<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['usuario_id'])) { header('Location: index.php'); exit; }

$erro = ''; $nome = '';
$ok = $_SESSION['flash_ok'] ?? ''; unset($_SESSION['flash_ok']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $erro = 'Sessão expirada. Recarregue a página e tente de novo.';
    } elseif ($nome === '' || $senha === '') {
        $erro = 'Preencha o usuário e a senha.';
    } else {
        try {
            $st = pdo()->prepare('SELECT id, nome, senha FROM usuarios WHERE nome = ?');
            $st->execute([$nome]);
            $u = $st->fetch(PDO::FETCH_ASSOC);
            $guardada = $u ? (string) $u['senha'] : '';
            $eHash    = password_get_info($guardada)['algo'] !== null;
            // Conta antiga com senha em texto puro: aceita uma vez e converte para hash.
            $confere  = $u && ($eHash ? password_verify($senha, $guardada) : hash_equals($guardada, $senha));
            if ($confere) {
                if (!$eHash || password_needs_rehash($guardada, PASSWORD_DEFAULT)) {
                    pdo()->prepare('UPDATE usuarios SET senha = ? WHERE id = ?')
                         ->execute([password_hash($senha, PASSWORD_DEFAULT), $u['id']]);
                }
                session_regenerate_id(true);
                $_SESSION['usuario_id']   = (int) $u['id'];
                $_SESSION['usuario_nome'] = $u['nome'];
                header('Location: index.php'); exit;
            }
            usleep(400000);
            $erro = 'Usuário ou senha incorretos.';
        } catch (PDOException $ex) {
            $erro = 'Não foi possível conectar ao banco de dados.';
        }
    }
}
$titulo = 'Entrar'; $classe = 'form-page';
require __DIR__ . '/inc/topo.php';
?>
<main class="cartao">
  <h1>Entrar</h1>
  <?php if ($ok):   ?><div class="aviso ok"><?= e($ok) ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="aviso erro" role="alert"><?= e($erro) ?></div><?php endif; ?>
  <form method="post" action="login.php">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <label for="nome">Usuário</label>
    <input id="nome" name="nome" maxlength="50" value="<?= e($nome) ?>" autocomplete="username" autofocus required>
    <label for="senha">Senha</label>
    <input id="senha" name="senha" type="password" maxlength="72" autocomplete="current-password" required>
    <button class="btn">Entrar</button>
  </form>
  <p class="rodape-cartao">Ainda não tem conta? <a href="cad.php">Criar conta</a> · <a href="index.php">Voltar às notícias</a></p>
</main>
<?php require __DIR__ . '/inc/rodape.php'; ?>
