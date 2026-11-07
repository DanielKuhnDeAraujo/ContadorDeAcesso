<?php
require __DIR__ . '/config.php';
if (!empty($_SESSION['usuario_id'])) { header('Location: index.php'); exit; } // logado não se cadastra

$erro = ''; $nome = ''; $nasc = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $nasc  = trim($_POST['birthdate'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $conf  = $_POST['confirma'] ?? '';
    $d = DateTime::createFromFormat('Y-m-d', $nasc);
    $dataOk = $d && $d->format('Y-m-d') === $nasc && $d <= new DateTime('today');

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $erro = 'Sessão expirada. Recarregue a página e tente de novo.';
    } elseif ($nome === '' || $nasc === '' || $senha === '' || $conf === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (mb_strlen($nome) > 50) {
        $erro = 'O usuário pode ter no máximo 50 caracteres.';
    } elseif (!$dataOk) {
        $erro = 'Informe uma data de nascimento válida.';
    } elseif (strlen($senha) < 6 || strlen($senha) > 72) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $conf) {
        $erro = 'As senhas não são iguais.';
    } else {
        try {
            $pdo = pdo();
            $st = $pdo->prepare('SELECT 1 FROM usuarios WHERE nome = ?');
            $st->execute([$nome]);
            if ($st->fetch()) {
                $erro = 'Já existe um usuário com esse nome. Escolha outro.';
            } else {
                $pdo->prepare('INSERT INTO usuarios (nome, birthdate, senha) VALUES (?, ?, ?)')
                    ->execute([$nome, $nasc, password_hash($senha, PASSWORD_DEFAULT)]);
                $_SESSION['flash_ok'] = 'Cadastro realizado. Entre com seu usuário e senha.';
                header('Location: login.php'); exit;
            }
        } catch (PDOException $ex) {
            $erro = 'Não foi possível salvar o cadastro. Verifique o banco de dados.';
        }
    }
}
$titulo = 'Criar conta'; $classe = 'form-page';
require __DIR__ . '/inc/topo.php';
?>
<main class="cartao">
  <h1>Criar conta</h1>
  <?php if ($erro): ?><div class="aviso erro" role="alert"><?= e($erro) ?></div><?php endif; ?>
  <form method="post" action="cad.php">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <label for="nome">Usuário</label>
    <input id="nome" name="nome" maxlength="50" value="<?= e($nome) ?>" autocomplete="username" autofocus required>
    <label for="birthdate">Data de nascimento</label>
    <input id="birthdate" name="birthdate" type="date" max="<?= date('Y-m-d') ?>" value="<?= e($nasc) ?>" required>
    <label for="senha">Senha (mínimo 6 caracteres)</label>
    <input id="senha" name="senha" type="password" minlength="6" maxlength="72" autocomplete="new-password" required>
    <label for="confirma">Repita a senha</label>
    <input id="confirma" name="confirma" type="password" maxlength="72" autocomplete="new-password" required>
    <button class="btn">Criar conta</button>
  </form>
  <p class="rodape-cartao">Já tem conta? <a href="login.php">Entrar</a> · <a href="index.php">Voltar às notícias</a></p>
</main>
<?php require __DIR__ . '/inc/rodape.php'; ?>
