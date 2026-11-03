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
