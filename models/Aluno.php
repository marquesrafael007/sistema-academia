<?php
class Aluno {
    public function listar(): array {
        // JOIN acrescenta o nome do plano ao resultado.
        $s=Database::getConnection()->prepare('SELECT a.*,p.nome AS plano_nome FROM alunos a JOIN planos p ON p.id=a.plano_id ORDER BY a.id_aluno DESC');
        $s->execute(); return $s->fetchAll();
    }
    public function buscar(int $id): ?array {
        $s=Database::getConnection()->prepare('SELECT a.*,p.nome AS plano_nome FROM alunos a JOIN planos p ON p.id=a.plano_id WHERE a.id_aluno=:id');
        $s->execute(['id'=>$id]); return $s->fetch()?:null;
    }
    public function salvar(array $dados,?int $id=null): int {
        $sql='INSERT INTO alunos (nome,idade,ativo,telefone,imagem_url,plano_id) VALUES (:nome,:idade,:ativo,:telefone,:imagem_url,:plano_id)';
        if ($id!==null) { $sql='UPDATE alunos SET nome=:nome,idade=:idade,ativo=:ativo,telefone=:telefone,imagem_url=:imagem_url,plano_id=:plano_id WHERE id_aluno=:id'; $dados['id']=$id; }
        $db=Database::getConnection(); $db->prepare($sql)->execute($dados);
        return $id??(int)$db->lastInsertId();
    }
    public function excluir(int $id): void {
        Database::getConnection()->prepare('DELETE FROM alunos WHERE id_aluno=:id')->execute(['id'=>$id]);
    }
}
