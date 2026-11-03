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
