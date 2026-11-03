<?php
class Database {
    private static $conexao=null;
    public static function getConnection(): PDO {
        // Ajuste usuário e senha se sua instalação não usar o padrão do XAMPP.
        if (self::$conexao===null) {
            self::$conexao=new PDO('mysql:host=127.0.0.1;dbname=academia;charset=utf8mb4','root','',[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES=>false
            ]);
        }
        return self::$conexao;
    }
}
