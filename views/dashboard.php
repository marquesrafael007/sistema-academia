<?php require __DIR__.'/partials/header.php'; ?>
<h1>Bem-vindo(a), <?= e($_SESSION['usuario_nome']) ?>!</h1>
<p>Use o menu para gerenciar os alunos e os planos da academia.</p>
<div class="d-flex gap-3 flex-wrap"><a class="btn btn-primary" href="index.php?controller=aluno&acao=listar">Gerenciar alunos</a>
<a class="btn btn-outline-primary" href="index.php?controller=plano&acao=listar">Gerenciar planos</a></div>
<?php require __DIR__.'/partials/footer.php'; ?>
