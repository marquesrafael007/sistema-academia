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
