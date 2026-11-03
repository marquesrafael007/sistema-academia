<?php

class AuthController
{
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: index.php?controller=usuario&acao=login');
            exit;
        }
    }

    public static function login(string $email, string $senha): bool
    {
        $db = Database::getConnection();
        $usuarioModel = new Usuario($db);
        $usuario = $usuarioModel->findByEmail($email);

        if (!$usuario) {
            return false;
        }

        if (!password_verify($senha, $usuario['senha'])) {
            return false;
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];

        return true;
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: index.php?controller=usuario&acao=login');
        exit;
    }
}
