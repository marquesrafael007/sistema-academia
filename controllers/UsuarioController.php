<?php
class UsuarioController {
    public function login(): void {
        if (isset($_SESSION['usuario_id'])) { redirect('usuario','painel'); }
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            $email=texto($_POST,'email');
            $senha=is_string($_POST['senha']??null)?$_POST['senha']:'';
            $u=filter_var($email,FILTER_VALIDATE_EMAIL)?(new Usuario())->buscarPorEmail($email):null;
            if ($u && password_verify($senha,$u['senha'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id']=$u['id']; $_SESSION['usuario_nome']=$u['nome'];
                unset($_SESSION['csrf']); redirect('usuario','painel');
            }
            setFlash('danger','E-mail ou senha inválidos.'); redirect('usuario','login');
        }
        require __DIR__.'/../views/login.php';
    }
    public function painel(): void { require __DIR__.'/../views/dashboard.php'; }
    public function logout(): void {
        $_SESSION=[];
        if (ini_get('session.use_cookies')) {
            $p=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
        }
        session_destroy(); redirect('usuario','login');
    }
}
