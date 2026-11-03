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
