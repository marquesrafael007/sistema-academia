<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-4"><?= !empty($categoria['id']) ? 'Editar categoria' : 'Nova categoria'; ?></h2>

                <form method="POST" action="index.php?controller=categoria&acao=salvar">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($categoria['id'] ?? ''); ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($categoria['nome'] ?? ''); ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="index.php?controller=categoria&acao=listar" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
