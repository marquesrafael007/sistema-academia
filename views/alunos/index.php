<?php require __DIR__.'/../partials/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Alunos</h1><a class="btn btn-primary" href="index.php?controller=aluno&acao=cadastrar">Cadastrar aluno</a></div>
<div class="table-responsive"><table class="table table-striped align-middle">
<thead><tr><th>ID</th><th>Nome</th><th>Idade</th><th>Ativo</th><th>Telefone</th><th>Plano</th><th>Imagem</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($alunos as $r): ?>
<tr><td><?= e($r['id_aluno']) ?></td><td><?= e($r['nome']) ?></td>
<td><?= e($r['idade']) ?></td><td><?= $r['ativo']?'Sim':'Não' ?></td><td><?= e($r['telefone']) ?></td><td><?= e($r['plano_nome']) ?></td>
<td><?php if ($r['imagem_url']): ?><img src="<?= e($r['imagem_url']) ?>" alt="Foto de <?= e($r['nome']) ?>" width="48" height="48" loading="lazy" style="object-fit:cover"><?php endif; ?></td>
<td><div class="d-flex gap-2">
<a class="btn btn-sm btn-outline-primary" href="index.php?controller=aluno&acao=editar&id=<?= e($r['id_aluno']) ?>">Editar</a>
<form method="post" action="index.php?controller=aluno&acao=excluir" onsubmit="return confirm('Excluir este registro?')">
<?php csrfInput(); ?><input type="hidden" name="id" value="<?= e($r['id_aluno']) ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form>
</div></td></tr><?php endforeach; ?>
<?php if (!$alunos): ?><tr><td colspan="8">Nenhum registro cadastrado.</td></tr><?php endif; ?>
</tbody></table></div><?php require __DIR__.'/../partials/footer.php'; ?>
