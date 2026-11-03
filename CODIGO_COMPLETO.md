# Código completo do sistema de academia


## banco.sql

Cria o banco, as tabelas e cinco exemplos em cada tabela.

```sql
-- Importe uma vez em uma instalação nova. Não apaga bancos existentes.
CREATE DATABASE IF NOT EXISTS academia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE academia;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE planos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE alunos (
    id_aluno INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    idade TINYINT UNSIGNED NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    telefone VARCHAR(25) NOT NULL,
    imagem_url VARCHAR(500) NOT NULL DEFAULT '',
    plano_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alunos_planos FOREIGN KEY (plano_id) REFERENCES planos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Hash gerado com password_hash('Academia@123', PASSWORD_DEFAULT).
-- Os cinco usuários didáticos usam a mesma senha: Academia@123.
INSERT INTO usuarios (nome,email,senha) VALUES
('Administrador','admin@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Ana Recepção','ana@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Bruno Gestão','bruno@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Carla Recepção','carla@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Diego Gestão','diego@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay');

INSERT INTO planos (nome,valor) VALUES
('Mensal',99.90),('Trimestral',269.90),('Semestral',499.90),('Anual',899.90),('Musculação e aulas',149.90);

INSERT INTO alunos (nome,idade,ativo,telefone,imagem_url,plano_id) VALUES
('Alice Santos',23,TRUE,'(11) 99911-1111','',1),
('Bruno Lima',30,TRUE,'(11) 99922-2222','',2),
('Camila Souza',28,TRUE,'(11) 99933-3333','',3),
('Daniel Costa',41,FALSE,'(11) 99944-4444','',4),
('Elisa Martins',35,TRUE,'(11) 99955-5555','',5);

```

## index.php

Roteador: verifica login, método HTTP e token antes de chamar a ação.

```php
<?php
require __DIR__.'/config/bootstrap.php';
require __DIR__.'/config/web.php';
session_start();
// Lista fechada: a URL não pode executar qualquer método das classes.
$c = $_GET['controller'] ?? 'usuario';
$a = $_GET['acao'] ?? 'login';
$rotas = ['usuario'=>['login','logout','painel'], 'aluno'=>['listar','cadastrar','editar','excluir'], 'plano'=>['listar','cadastrar','editar','excluir']];
if (!is_string($c) || !is_string($a) || !isset($rotas[$c]) || !in_array($a,$rotas[$c],true)) { http_response_code(404); exit('Página não encontrada.'); }
if (!isset($_SESSION['usuario_id']) && !($c==='usuario' && $a==='login')) { redirect('usuario','login'); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals(csrf(),$_POST['csrf'])) { http_response_code(400); exit('Formulário expirado. Atualize a página.'); }
} elseif ($_SERVER['REQUEST_METHOD']!=='GET' || in_array($a,['excluir','logout'],true)) {
    header('Allow: POST'); http_response_code(405); exit('Método não permitido.');
}
try { $classe=ucfirst($c).'Controller'; (new $classe())->$a(); }
catch (Throwable $erro) { error_log((string)$erro); erroPagina(500,'Não foi possível concluir. Confira a configuração do banco e tente novamente.'); }

```

## config/Database.php

Abre uma única conexão PDO e configura erros como exceções.

```php
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

```

## config/bootstrap.php

Carrega os arquivos necessários sem autoload ou Composer.

```php
<?php
// Carregamento manual, sem Composer.
require_once __DIR__.'/Database.php';
require_once __DIR__.'/validacao.php';
foreach (['Usuario','Aluno','Plano'] as $nome) {
    require_once __DIR__.'/../models/'.$nome.'.php';
    require_once __DIR__.'/../controllers/'.$nome.'Controller.php';
}

```

## config/web.php

Funções pequenas para HTML seguro, mensagens, redirecionamentos e CSRF.

```php
<?php
// Sempre escape os dados ao exibi-los no HTML.
function e($valor): string { return htmlspecialchars((string)$valor,ENT_QUOTES,'UTF-8'); }
function redirect(string $c,string $a='listar'): void { header('Location: index.php?controller='.$c.'&acao='.$a); exit; }
function setFlash(string $type,string $message): void { $_SESSION['flash']=compact('type','message'); }
function getFlash(): ?array { $f=$_SESSION['flash']??null; unset($_SESSION['flash']); return $f; }
// Token protege formulários de envios feitos por outros sites.
function csrf(): string {
    if (!isset($_SESSION['csrf'])) { $_SESSION['csrf']=bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrfInput(): void { echo '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function erroPagina(int $status,string $mensagem): void { http_response_code($status); require __DIR__.'/../views/erro.php'; }

```

## config/validacao.php

Regras compartilhadas entre site e API. Retorna dados tratados e uma lista de erros.

```php
<?php
function texto(array $d,string $campo): string { return is_string($d[$campo]??null)?trim($d[$campo]):''; }
function idValido($v): ?int {
    if (!is_string($v) && !is_int($v)) { return null; }
    $id=filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    return $id===false?null:$id;
}
function validarPlano(array $d): array {
    $valor=$d['valor']??'';
    $dados=['nome'=>texto($d,'nome'),'valor'=>is_scalar($valor)?str_replace(',','.',(string)$valor):''];
    $erros=[];
    if ($dados['nome']==='' || strlen($dados['nome'])>100) { $erros[]='Informe um nome de até 100 bytes.'; }
    if (!preg_match('/^\\d{1,8}(\\.\\d{1,2})?$/D',$dados['valor'])) { $erros[]='Valor inválido: use de 0 a 99999999,99, com até duas casas decimais.'; }
    return [$dados,$erros];
}
function validarAluno(array $d): array {
    $dados=['nome'=>texto($d,'nome'),'idade'=>$d['idade']??'', 'ativo'=>$d['ativo']??null,
        'telefone'=>texto($d,'telefone'),'imagem_url'=>texto($d,'imagem_url'),'plano_id'=>idValido($d['plano_id']??null)];
    $erros=[];
    if ($dados['nome']==='' || strlen($dados['nome'])>100) { $erros[]='Informe um nome de até 100 bytes.'; }
    $idade=(is_string($dados['idade'])||is_int($dados['idade']))?filter_var($dados['idade'],FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>120]]):false;
    if ($idade===false) { $erros[]='Idade deve ser um inteiro entre 1 e 120.'; }
    $dados['idade']=$idade===false?'':$idade;
    if (!in_array($dados['ativo'],[true,false,0,1,'0','1'],true)) { $erros[]='Ativo deve ser true/false ou 1/0.'; }
    $dados['ativo']=in_array($dados['ativo'],[true,1,'1'],true)?1:0;
    if (!preg_match('/^[0-9+() .-]{8,25}$/D',$dados['telefone'])) { $erros[]='Informe telefone de 8 a 25 caracteres.'; }
    if ($dados['imagem_url']!=='' && (strlen($dados['imagem_url'])>500 || !filter_var($dados['imagem_url'],FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($dados['imagem_url'],PHP_URL_SCHEME)??''),['http','https'],true))) { $erros[]='Imagem deve ser URL http/https de até 500 caracteres.'; }
    if (!$dados['plano_id'] || !(new Plano())->buscar($dados['plano_id'])) { $erros[]='Escolha um plano existente.'; }
    return [$dados,$erros];
}

```

## models/Usuario.php

Busca usuários e mostra como armazenar uma nova senha usando hash.

```php
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

```

## models/Plano.php

Consultas preparadas para todas as operações de planos.

```php
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

```

## models/Aluno.php

Consultas de alunos; JOIN busca o nome do plano relacionado.

```php
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

```

## controllers/UsuarioController.php

Recebe a ação, valida os dados e conecta model e view.

```php
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

```

## controllers/AlunoController.php

Recebe a ação, valida os dados e conecta model e view.

```php
<?php
class AlunoController {
    public function listar(): void {
        $alunos=(new Aluno())->listar();
        require __DIR__.'/../views/alunos/index.php';
    }
    public function cadastrar(): void { $this->formulario(); }
    public function editar(): void {
        $id=idValido($_GET['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $this->formulario($id);
    }
    private function formulario(?int $id=null): void {
        $model=new Aluno();
        $dados=$id?$model->buscar($id):['nome'=>'','idade'=>'','ativo'=>1,'telefone'=>'','imagem_url'=>'','plano_id'=>''];
        if (!$dados) { erroPagina(404,'Registro não encontrado.'); return; }
        $erros=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            [$dados,$erros]=validarAluno($_POST);
            if (!$erros) { $model->salvar($dados,$id); setFlash('success','Registro salvo.'); redirect('aluno'); }
            http_response_code(400);
        }
        $planos=(new Plano())->listar();
        require __DIR__.'/../views/alunos/form.php';
    }
    public function excluir(): void {
        $id=idValido($_POST['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $model=new Aluno();
        if (!$model->buscar($id)) { erroPagina(404,'Registro não encontrado.'); return; }
        try { $model->excluir($id); setFlash('success','Registro excluído.'); }
        catch (PDOException $erro) {
            if (($erro->errorInfo[1]??null)!=1451) { throw $erro; }
            setFlash('danger','Este plano possui alunos. Altere o plano desses alunos antes de excluir.');
        }
        redirect('aluno');
    }
}

```

## controllers/PlanoController.php

Recebe a ação, valida os dados e conecta model e view.

```php
<?php
class PlanoController {
    public function listar(): void {
        $planos=(new Plano())->listar();
        require __DIR__.'/../views/planos/index.php';
    }
    public function cadastrar(): void { $this->formulario(); }
    public function editar(): void {
        $id=idValido($_GET['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $this->formulario($id);
    }
    private function formulario(?int $id=null): void {
        $model=new Plano();
        $dados=$id?$model->buscar($id):['nome'=>'','valor'=>''];
        if (!$dados) { erroPagina(404,'Registro não encontrado.'); return; }
        $erros=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            [$dados,$erros]=validarPlano($_POST);
            if (!$erros) { $model->salvar($dados,$id); setFlash('success','Registro salvo.'); redirect('plano'); }
            http_response_code(400);
        }
        
        require __DIR__.'/../views/planos/form.php';
    }
    public function excluir(): void {
        $id=idValido($_POST['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $model=new Plano();
        if (!$model->buscar($id)) { erroPagina(404,'Registro não encontrado.'); return; }
        try { $model->excluir($id); setFlash('success','Registro excluído.'); }
        catch (PDOException $erro) {
            if (($erro->errorInfo[1]??null)!=1451) { throw $erro; }
            setFlash('danger','Este plano possui alunos. Altere o plano desses alunos antes de excluir.');
        }
        redirect('plano');
    }
}

```

## views/partials/header.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php $flash=getFlash(); ?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Academia</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container d-flex flex-wrap gap-3">
<a class="navbar-brand" href="index.php?controller=usuario&acao=painel">Academia</a>
<?php if (isset($_SESSION['usuario_id'])): ?>
<div class="d-flex flex-wrap align-items-center gap-3">
<a class="text-white" href="index.php?controller=aluno&acao=listar">Alunos</a>
<a class="text-white" href="index.php?controller=plano&acao=listar">Planos</a>
<span class="text-white"><?= e($_SESSION['usuario_nome']) ?></span>
<form method="post" action="index.php?controller=usuario&acao=logout"><?php csrfInput(); ?><button class="btn btn-outline-light btn-sm">Sair</button></form>
</div><?php endif; ?></div></nav><main class="container py-4">
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div><?php endif; ?>

```

## views/partials/footer.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
</main></body></html>

```

## views/partials/erros.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php if ($erros): ?>
<div class="alert alert-danger" role="alert"><ul class="mb-0">
<?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
</ul></div><?php endif; ?>

```

## views/login.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/partials/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card card-body shadow-sm">
<h1 class="h3">Entrar na academia</h1>
<form method="post" action="index.php?controller=usuario&acao=login">
<?php csrfInput(); ?>
<label for="email" class="form-label">E-mail</label><input class="form-control mb-3" type="email" id="email" name="email" autocomplete="username" required>
<label for="senha" class="form-label">Senha</label><input class="form-control mb-3" type="password" id="senha" name="senha" autocomplete="current-password" required>
<button class="btn btn-primary w-100">Entrar</button></form></div></div></div>
<?php require __DIR__.'/partials/footer.php'; ?>

```

## views/dashboard.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/partials/header.php'; ?>
<h1>Bem-vindo(a), <?= e($_SESSION['usuario_nome']) ?>!</h1>
<p>Use o menu para gerenciar os alunos e os planos da academia.</p>
<div class="d-flex gap-3 flex-wrap"><a class="btn btn-primary" href="index.php?controller=aluno&acao=listar">Gerenciar alunos</a>
<a class="btn btn-outline-primary" href="index.php?controller=plano&acao=listar">Gerenciar planos</a></div>
<?php require __DIR__.'/partials/footer.php'; ?>

```

## views/erro.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/partials/header.php'; ?>
<div class="alert alert-danger"><?= e($mensagem) ?></div><a href="index.php">Voltar ao início</a>
<?php require __DIR__.'/partials/footer.php'; ?>

```

## views/alunos/form.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/../partials/header.php'; ?>
<h1><?= $id?'Editar':'Cadastrar' ?> aluno</h1>
<?php require __DIR__.'/../partials/erros.php'; ?>
<?php if (!$planos): ?><div class="alert alert-warning">Cadastre um plano antes de cadastrar alunos.</div><?php endif; ?>
<form method="post" action="index.php?controller=aluno&acao=<?= $id?'editar&id='.e($id):'cadastrar' ?>" class="card card-body">
<?php csrfInput(); ?>
<div class="row g-3">
<div class="col-md-8"><label for="nome" class="form-label">Nome</label><input id="nome" name="nome" class="form-control" maxlength="100" value="<?= e($dados['nome']) ?>" required></div>
<div class="col-md-4"><label for="idade" class="form-label">Idade</label><input type="number" id="idade" name="idade" class="form-control" min="1" max="120" value="<?= e($dados['idade']) ?>" required></div>
<div class="col-md-6"><label for="telefone" class="form-label">Telefone</label><input type="tel" id="telefone" name="telefone" class="form-control" maxlength="25" value="<?= e($dados['telefone']) ?>" required></div>
<div class="col-md-6"><label for="plano_id" class="form-label">Plano</label><select id="plano_id" name="plano_id" class="form-select" required>
<option value="">Selecione</option><?php foreach ($planos as $p): ?><option value="<?= e($p['id']) ?>" <?= (string)$dados['plano_id']===(string)$p['id']?'selected':'' ?>><?= e($p['nome']) ?></option><?php endforeach; ?>
</select></div>
<div class="col-md-9"><label for="imagem_url" class="form-label">URL da imagem (opcional)</label><input type="url" id="imagem_url" name="imagem_url" class="form-control" maxlength="500" value="<?= e($dados['imagem_url']) ?>"></div>
<div class="col-md-3"><label for="ativo" class="form-label">Ativo</label><select id="ativo" name="ativo" class="form-select"><option value="1" <?= $dados['ativo']?'selected':'' ?>>Sim</option><option value="0" <?= !$dados['ativo']?'selected':'' ?>>Não</option></select></div>
</div><div class="mt-3"><button class="btn btn-primary" <?= !$planos?'disabled':'' ?>>Salvar</button> <a class="btn btn-secondary" href="index.php?controller=aluno&acao=listar">Cancelar</a></div>
</form><?php require __DIR__.'/../partials/footer.php'; ?>

```

## views/planos/form.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/../partials/header.php'; ?>
<h1><?= $id?'Editar':'Cadastrar' ?> plano</h1>
<?php require __DIR__.'/../partials/erros.php'; ?>
<form method="post" action="index.php?controller=plano&acao=<?= $id?'editar&id='.e($id):'cadastrar' ?>" class="card card-body">
<?php csrfInput(); ?>
<label for="nome" class="form-label">Nome</label><input id="nome" name="nome" class="form-control mb-3" maxlength="100" value="<?= e($dados['nome']) ?>" required>
<label for="valor" class="form-label">Valor (R$)</label><input type="number" id="valor" name="valor" class="form-control mb-3" min="0" max="99999999.99" step="0.01" value="<?= e($dados['valor']) ?>" required>
<div><button class="btn btn-primary">Salvar</button> <a class="btn btn-secondary" href="index.php?controller=plano&acao=listar">Cancelar</a></div></form>
<?php require __DIR__.'/../partials/footer.php'; ?>

```

## api/bootstrap.php

Cabeçalhos JSON/CORS, tratamento central de exceções e métodos HTTP.

```php
<?php
// A API não depende da sessão do site. CORS permite chamadas de outros domínios.
ini_set('display_errors','0');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
function jsonResposta($dados,int $status=200): void {
    http_response_code($status);
    echo json_encode($dados,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
set_exception_handler(function(Throwable $erro): void {
    error_log((string)$erro);
    jsonResposta(['erro'=>'Erro interno. Confira o banco de dados e tente novamente.'],500);
});
require_once __DIR__.'/../config/bootstrap.php';
function metodos(array $permitidos): string {
    $permitidos[]='OPTIONS';
    header('Access-Control-Allow-Methods: '.implode(', ',$permitidos));
    $metodo=$_SERVER['REQUEST_METHOD'];
    if ($metodo==='OPTIONS') { http_response_code(204); exit; }
    if (!in_array($metodo,$permitidos,true)) {
        header('Allow: '.implode(', ',$permitidos));
        jsonResposta(['erro'=>'Método não permitido.'],405);
    }
    return $metodo;
}
function alunoJson(array $a): array {
    foreach (['id_aluno','idade','plano_id'] as $campo) { $a[$campo]=(int)$a[$campo]; }
    $a['ativo']=(bool)$a['ativo'];
    return $a;
}

```

## api/alunos.php

Implementa listagem, busca por ID e criação a partir de JSON.

```php
<?php
require __DIR__.'/bootstrap.php';
$metodo=metodos(['GET','POST']);
$model=new Aluno();
if ($metodo==='GET') {
    if (array_key_exists('id',$_GET)) {
        $id=idValido($_GET['id']);
        if (!$id) { jsonResposta(['erro'=>'ID inválido.'],400); }
        $aluno=$model->buscar($id);
        if (!$aluno) { jsonResposta(['erro'=>'Aluno não encontrado.'],404); }
        jsonResposta(alunoJson($aluno));
    }
    jsonResposta(array_map('alunoJson',$model->listar()));
}
if (strtolower(trim(explode(';',$_SERVER['CONTENT_TYPE']??'')[0]))!=='application/json') {
    jsonResposta(['erro'=>'Envie Content-Type: application/json.'],400);
}
try { $obj=json_decode(file_get_contents('php://input'),false,512,JSON_THROW_ON_ERROR); }
catch (JsonException $erro) { jsonResposta(['erro'=>'JSON inválido.'],400); }
if (!is_object($obj)) { jsonResposta(['erro'=>'O corpo deve ser um objeto JSON.'],400); }
[$dados,$erros]=validarAluno((array)$obj);
if ($erros) { jsonResposta(['erro'=>'Dados inválidos.','detalhes'=>$erros],400); }
$id=$model->salvar($dados);
header('Location: alunos.php?id='.$id);
jsonResposta(alunoJson($model->buscar($id)),201);

```

## api/planos.php

Lista os planos em JSON.

```php
<?php
require __DIR__.'/bootstrap.php';
metodos(['GET']);
$planos=(new Plano())->listar();
foreach ($planos as &$plano) { $plano['id']=(int)$plano['id']; }
// Valor permanece string decimal para não perder precisão monetária.
unset($plano);
jsonResposta($planos);

```

## views/alunos/index.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/../partials/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Alunos</h1><a class="btn btn-primary" href="index.php?controller=aluno&acao=cadastrar">Cadastrar aluno</a></div>
<div class="table-responsive"><table class="table table-striped align-middle">
<thead><tr><th>ID</th><th>Nome</th><th>Idade</th><th>Ativo</th><th>Telefone</th><th>Plano</th><th>Imagem</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($alunos as $r): ?>
<tr><td><?= e($r['id_aluno']) ?></td><td><?= e($r['nome']) ?></td>
<td><?= e($r['idade']) ?></td><td><?= $r['ativo']?'Sim':'Não' ?></td><td><?= e($r['telefone']) ?></td><td><?= e($r['plano_nome']) ?></td>
<td><?php if ($r['imagem_url']): ?><img src="<?= e($r['imagem_url']) ?>" alt="Foto de <?= e($r['nome']) ?>" width="48" height="48" loading="lazy" style="object-fit:cover"><?php endif; ?></td>
<td><div class="d-flex gap-2">
<a class="btn btn-sm btn-outline-primary" href="index.php?controller=aluno&acao=editar&id=<?= e($r['id_aluno']) ?>">Editar</a>
<form method="post" action="index.php?controller=aluno&acao=excluir" onsubmit="return confirm('Excluir este registro?')">
<?php csrfInput(); ?><input type="hidden" name="id" value="<?= e($r['id_aluno']) ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form>
</div></td></tr><?php endforeach; ?>
<?php if (!$alunos): ?><tr><td colspan="8">Nenhum registro cadastrado.</td></tr><?php endif; ?>
</tbody></table></div><?php require __DIR__.'/../partials/footer.php'; ?>

```

## views/planos/index.php

Exibe o conteúdo em HTML, escapando os dados vindos do usuário.

```php
<?php require __DIR__.'/../partials/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Planos</h1><a class="btn btn-primary" href="index.php?controller=plano&acao=cadastrar">Cadastrar plano</a></div>
<div class="table-responsive"><table class="table table-striped align-middle">
<thead><tr><th>ID</th><th>Nome</th><th>Valor</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($planos as $r): ?>
<tr><td><?= e($r['id']) ?></td><td><?= e($r['nome']) ?></td>
<td>R$ <?= e(number_format((float)$r['valor'],2,',','.')) ?></td>
<td><div class="d-flex gap-2">
<a class="btn btn-sm btn-outline-primary" href="index.php?controller=plano&acao=editar&id=<?= e($r['id']) ?>">Editar</a>
<form method="post" action="index.php?controller=plano&acao=excluir" onsubmit="return confirm('Excluir este registro?')">
<?php csrfInput(); ?><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn btn-sm btn-outline-danger">Excluir</button></form>
</div></td></tr><?php endforeach; ?>
<?php if (!$planos): ?><tr><td colspan="4">Nenhum registro cadastrado.</td></tr><?php endif; ?>
</tbody></table></div><?php require __DIR__.'/../partials/footer.php'; ?>

```
