<?php
require __DIR__ . '/config.php';
require __DIR__ . '/contador.php';

$visitas  = numero_visitas();          // antes de qualquer HTML (usa cookie)
$logado   = !empty($_SESSION['usuario_id']);
$noticias = require __DIR__ . '/noticias.php';
$titulo   = 'Portal Informática';
require __DIR__ . '/inc/topo.php';
?>
<header class="barra">
  <a class="marca" href="index.php">Portal Informática</a>
  <nav>
  <?php if ($logado): ?>
    <span class="ola">Olá, <?= e($_SESSION['usuario_nome']) ?></span>
    <form method="post" action="logout.php" class="inline">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
      <button class="btn-sec">Sair</button>
    </form>
  <?php else: ?>
    <a class="btn-sec" href="login.php">Entrar</a>
    <a class="btn" href="cad.php">Criar conta</a>
  <?php endif; ?>
  </nav>
</header>

<main class="conteudo">
  <section class="topo">
    <h1>Notícias de informática</h1>
    <p class="visitas"><strong><?= number_format($visitas, 0, ',', '.') ?></strong> visitas</p>
  </section>

  <?php foreach ($noticias as $n): ?>
    <article class="noticia">
      <p class="meta"><?= e($n['categoria']) ?> · <?= date('d/m/Y', strtotime($n['data'])) ?></p>
      <h2><?= e($n['titulo']) ?></h2>
      <p><?= e($n['resumo']) ?></p>
    </article>
  <?php endforeach; ?>
</main>
<?php require __DIR__ . '/inc/rodape.php'; ?>
