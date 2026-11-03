<?php require __DIR__.'/../partials/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Planos</h1><a class="btn btn-primary" href="index.php?controller=plano&acao=cadastrar">Cadastrar plano</a></div>
<div class="table-responsive"><table class="table table-striped align-middle">
<thead><tr><th>ID</th><th>Nome</th><th>Valor</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($planos as $r): ?>
<tr><td><?= e($r['id']) ?></td><td><?= e($r['nome']) ?></td>
<td>R$ <?= e(number_format((float)$r['valor'],2,',','.')) ?></td>
<td><div class="d-flex gap-2">
<a class="btn btn-sm btn-outline-primary" href="index.php?controller=plano&acao=editar&id=<?= e($r['id']) ?>">Editar</a>
<form method="post" action="index.php?controller=plano&acao=excluir" onsubmit="return confirm('Excluir este registro?')">
<?php csrfInput(); ?><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form>
</div></td></tr><?php endforeach; ?>
<?php if (!$planos): ?><tr><td colspan="4">Nenhum registro cadastrado.</td></tr><?php endif; ?>
</tbody></table></div><?php require __DIR__.'/../partials/footer.php'; ?>
