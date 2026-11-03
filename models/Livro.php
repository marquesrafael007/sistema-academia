<?php

class Livro
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $sql = 'SELECT l.*, c.nome AS categoria_nome
                FROM livros l
                INNER JOIN categorias c ON c.id = l.categoria_id
                ORDER BY l.titulo ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT l.*, c.nome AS categoria_nome
                FROM livros l
                INNER JOIN categorias c ON c.id = l.categoria_id
                WHERE l.id = :id LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $livro = $stmt->fetch();
        return $livro ?: null;
    }

    public function create(array $dados): bool
    {
        $sql = 'INSERT INTO livros (titulo, autor, ano_publicacao, quantidade, imagem_url, categoria_id, criado_em)
                VALUES (:titulo, :autor, :ano_publicacao, :quantidade, :imagem_url, :categoria_id, NOW())';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $dados['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':autor', $dados['autor'], PDO::PARAM_STR);
        $stmt->bindParam(':ano_publicacao', $dados['ano_publicacao'], PDO::PARAM_INT);
        $stmt->bindParam(':quantidade', $dados['quantidade'], PDO::PARAM_INT);
        $stmt->bindParam(':imagem_url', $dados['imagem_url'], PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $dados['categoria_id'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function update(int $id, array $dados): bool
    {
        $sql = 'UPDATE livros
                SET titulo = :titulo,
                    autor = :autor,
                    ano_publicacao = :ano_publicacao,
                    quantidade = :quantidade,
                    imagem_url = :imagem_url,
                    categoria_id = :categoria_id
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $dados['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':autor', $dados['autor'], PDO::PARAM_STR);
        $stmt->bindParam(':ano_publicacao', $dados['ano_publicacao'], PDO::PARAM_INT);
        $stmt->bindParam(':quantidade', $dados['quantidade'], PDO::PARAM_INT);
        $stmt->bindParam(':imagem_url', $dados['imagem_url'], PDO::PARAM_STR);
        $stmt->bindParam(':categoria_id', $dados['categoria_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM livros WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
