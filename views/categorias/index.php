<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categorias</h2>
    <a href="index.php?controller=categoria&acao=cadastrar" class="btn btn-primary">Nova categoria</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categorias)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4">Nenhuma categoria cadastrada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= (int) $categoria['id']; ?></td>
                            <td><?= htmlspecialchars($categoria['nome']); ?></td>
                            <td class="text-end">
                                <a href="index.php?controller=categoria&acao=editar&id=<?= (int) $categoria['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="index.php?controller=categoria&acao=excluir&id=<?= (int) $categoria['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir esta categoria?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
