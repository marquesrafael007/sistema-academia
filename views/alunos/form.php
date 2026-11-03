<?php require __DIR__.'/../partials/header.php'; ?>
<h1><?= $id?'Editar':'Cadastrar' ?> aluno</h1>
<?php require __DIR__.'/../partials/erros.php'; ?>
<?php if (!$planos): ?><div class="alert alert-warning">Cadastre um plano antes de cadastrar alunos.</div><?php endif; ?>
<form method="post" action="index.php?controller=aluno&acao=<?= $id?'editar&id='.e($id):'cadastrar' ?>" class="card card-body">
<?php csrfInput(); ?>
<div class="row g-3">
<div class="col-md-8"><label for="nome" class="form-label">Nome</label><input id="nome" name="nome" class="form-control" maxlength="100" value="<?= e($dados['nome']) ?>" required></div>
<div class="col-md-4"><label for="idade" class="form-label">Idade</label><input type="number" id="idade" name="idade" class="form-control" min="1" max="120" value="<?= e($dados['idade']) ?>" required></div>
<div class="col-md-6"><label for="telefone" class="form-label">Telefone</label><input type="tel" id="telefone" name="telefone" class="form-control" maxlength="25" value="<?= e($dados['telefone']) ?>" required></div>
<div class="col-md-6"><label for="plano_id" class="form-label">Plano</label><select id="plano_id" name="plano_id" class="form-select" required>
<option value="">Selecione</option><?php foreach ($planos as $p): ?><option value="<?= e($p['id']) ?>" <?= (string)$dados['plano_id']===(string)$p['id']?'selected':'' ?>><?= e($p['nome']) ?></option><?php endforeach; ?>
</select></div>
<div class="col-md-9"><label for="imagem_url" class="form-label">URL da imagem (opcional)</label><input type="url" id="imagem_url" name="imagem_url" class="form-control" maxlength="500" value="<?= e($dados['imagem_url']) ?>"></div>
<div class="col-md-3"><label for="ativo" class="form-label">Ativo</label><select id="ativo" name="ativo" class="form-select"><option value="1" <?= $dados['ativo']?'selected':'' ?>>Sim</option><option value="0" <?= !$dados['ativo']?'selected':'' ?>>Não</option></select></div>
</div><div class="mt-3"><button class="btn btn-primary" <?= !$planos?'disabled':'' ?>>Salvar</button> <a class="btn btn-secondary" href="index.php?controller=aluno&acao=listar">Cancelar</a></div>
</form><?php require __DIR__.'/../partials/footer.php'; ?>
