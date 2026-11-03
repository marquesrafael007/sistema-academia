<?php
class Usuario {
    public function buscarPorEmail(string $email): ?array {
        $s=Database::getConnection()->prepare('SELECT * FROM usuarios WHERE email=:email');
        $s->execute(['email'=>$email]); return $s->fetch()?:null;
    }
    // A senha recebida nunca é armazenada em texto puro.
    public function criar(string $nome,string $email,string $senha): void {
        Database::getConnection()->prepare('INSERT INTO usuarios (nome,email,senha) VALUES (:nome,:email,:senha)')
            ->execute(['nome'=>$nome,'email'=>$email,'senha'=>password_hash($senha,PASSWORD_DEFAULT)]);
    }
}
