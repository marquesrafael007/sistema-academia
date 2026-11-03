<?php

class CategoriaController
{
    public function listar(): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $categoriaModel = new Categoria($db);
        $categorias = $categoriaModel->all();

        require __DIR__ . '/../views/categorias/index.php';
    }

    public function cadastrar(): void
    {
        AuthController::requireLogin();
        $categoria = ['id' => '', 'nome' => ''];
        require __DIR__ . '/../views/categorias/form.php';
    }

    public function editar(int $id): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $categoriaModel = new Categoria($db);
        $categoria = $categoriaModel->findById($id);

        if (!$categoria) {
            setFlash('danger', 'Categoria não encontrada.');
            header('Location: index.php?controller=categoria&acao=listar');
            exit;
        }

        require __DIR__ . '/../views/categorias/form.php';
    }

    public function salvar(): void
    {
        AuthController::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=categoria&acao=listar');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');

        if (empty($nome)) {
            setFlash('danger', 'O campo nome da categoria é obrigatório.');
            header('Location: index.php?controller=categoria&acao=cadastrar');
            exit;
        }

        $db = Database::getConnection();
        $categoriaModel = new Categoria($db);

        if (!empty($_POST['id'])) {
            $categoriaModel->update((int) $_POST['id'], $nome);
            setFlash('success', 'Categoria atualizada com sucesso!');
        } else {
            $categoriaModel->create($nome);
            setFlash('success', 'Categoria cadastrada com sucesso!');
        }

        header('Location: index.php?controller=categoria&acao=listar');
        exit;
    }

    public function excluir(int $id): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $categoriaModel = new Categoria($db);

        if ($categoriaModel->delete($id)) {
            setFlash('success', 'Categoria excluída com sucesso!');
        } else {
            setFlash('danger', 'Não foi possível excluir a categoria.');
        }

        header('Location: index.php?controller=categoria&acao=listar');
        exit;
    }
}
