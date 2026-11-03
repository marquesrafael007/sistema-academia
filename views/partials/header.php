<?php $flash=getFlash(); ?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Academia</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container d-flex flex-wrap gap-3">
<a class="navbar-brand" href="index.php?controller=usuario&acao=painel">Academia</a>
<?php if (isset($_SESSION['usuario_id'])): ?>
<div class="d-flex flex-wrap align-items-center gap-3">
<a class="text-white" href="index.php?controller=aluno&acao=listar">Alunos</a>
<a class="text-white" href="index.php?controller=plano&acao=listar">Planos</a>
<span class="text-white"><?= e($_SESSION['usuario_nome']) ?></span>
<form method="post" action="index.php?controller=usuario&acao=logout"><?php csrfInput(); ?><button class="btn btn-outline-light btn-sm">Sair</button></form>
</div><?php endif; ?></div></nav><main class="container py-4">
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div><?php endif; ?>
