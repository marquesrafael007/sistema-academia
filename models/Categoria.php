<?php

class Categoria
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $sql = 'SELECT * FROM categorias ORDER BY nome ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM categorias WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $categoria = $stmt->fetch();
        return $categoria ?: null;
    }

    public function create(string $nome): bool
    {
        $sql = 'INSERT INTO categorias (nome) VALUES (:nome)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function update(int $id, string $nome): bool
    {
        $sql = 'UPDATE categorias SET nome = :nome WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM categorias WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
