<?php

class LivroController
{
    public function listar(): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $livroModel = new Livro($db);
        $livros = $livroModel->all();

        require __DIR__ . '/../views/livros/index.php';
    }

    public function cadastrar(): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $categoriaModel = new Categoria($db);
        $categorias = $categoriaModel->all();
        $livro = [
            'id' => '',
            'titulo' => '',
            'autor' => '',
            'ano_publicacao' => '',
            'quantidade' => '',
            'imagem_url' => '',
            'categoria_id' => '',
        ];

        require __DIR__ . '/../views/livros/form.php';
    }

    public function editar(int $id): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $livroModel = new Livro($db);
        $categoriaModel = new Categoria($db);

        $livro = $livroModel->findById($id);
        $categorias = $categoriaModel->all();

        if (!$livro) {
            setFlash('danger', 'Livro não encontrado.');
            header('Location: index.php?controller=livro&acao=listar');
            exit;
        }

        require __DIR__ . '/../views/livros/form.php';
    }

    public function salvar(): void
    {
        AuthController::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=livro&acao=listar');
            exit;
        }

        $dados = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'autor' => trim($_POST['autor'] ?? ''),
            'ano_publicacao' => (int) ($_POST['ano_publicacao'] ?? 0),
            'quantidade' => (int) ($_POST['quantidade'] ?? 0),
            'imagem_url' => trim($_POST['imagem_url'] ?? ''),
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
        ];

        if (empty($dados['titulo']) || empty($dados['autor']) || $dados['ano_publicacao'] <= 0 || $dados['quantidade'] < 0 || empty($dados['categoria_id'])) {
            setFlash('danger', 'Preencha todos os campos obrigatórios corretamente.');
            header('Location: index.php?controller=livro&acao=cadastrar');
            exit;
        }

        $db = Database::getConnection();
        $livroModel = new Livro($db);

        if (!empty($_POST['id'])) {
            $livroModel->update((int) $_POST['id'], $dados);
            setFlash('success', 'Livro atualizado com sucesso!');
        } else {
            $livroModel->create($dados);
            setFlash('success', 'Livro cadastrado com sucesso!');
        }

        header('Location: index.php?controller=livro&acao=listar');
        exit;
    }

    public function excluir(int $id): void
    {
        AuthController::requireLogin();

        $db = Database::getConnection();
        $livroModel = new Livro($db);

        if ($livroModel->delete($id)) {
            setFlash('success', 'Livro excluído com sucesso!');
        } else {
            setFlash('danger', 'Não foi possível excluir o livro.');
        }

        header('Location: index.php?controller=livro&acao=listar');
        exit;
    }
}
