<?php require __DIR__.'/../partials/header.php'; ?>
<h1><?= $id?'Editar':'Cadastrar' ?> plano</h1>
<?php require __DIR__.'/../partials/erros.php'; ?>
<form method="post" action="index.php?controller=plano&acao=<?= $id?'editar&id='.e($id):'cadastrar' ?>" class="card card-body">
<?php csrfInput(); ?>
<label for="nome" class="form-label">Nome</label><input id="nome" name="nome" class="form-control mb-3" maxlength="100" value="<?= e($dados['nome']) ?>" required>
<label for="valor" class="form-label">Valor (R$)</label><input type="number" id="valor" name="valor" class="form-control mb-3" min="0" max="99999999.99" step="0.01" value="<?= e($dados['valor']) ?>" required>
<div><button class="btn btn-primary">Salvar</button> <a class="btn btn-secondary" href="index.php?controller=plano&acao=listar">Cancelar</a></div></form>
<?php require __DIR__.'/../partials/footer.php'; ?>
