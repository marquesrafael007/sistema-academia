<?php
class Plano {
    public function listar(): array {
        $s=Database::getConnection()->prepare('SELECT * FROM planos ORDER BY nome');
        $s->execute(); return $s->fetchAll();
    }
    public function buscar(int $id): ?array {
        $s=Database::getConnection()->prepare('SELECT * FROM planos WHERE id=:id');
        $s->execute(['id'=>$id]); return $s->fetch()?:null;
    }
    public function salvar(array $dados,?int $id=null): void {
        $sql='INSERT INTO planos (nome,valor) VALUES (:nome,:valor)';
        if ($id!==null) { $sql='UPDATE planos SET nome=:nome,valor=:valor WHERE id=:id'; $dados['id']=$id; }
        Database::getConnection()->prepare($sql)->execute($dados);
    }
    public function excluir(int $id): void {
        // A chave estrangeira protege planos que ainda têm alunos.
        Database::getConnection()->prepare('DELETE FROM planos WHERE id=:id')->execute(['id'=>$id]);
    }
}
