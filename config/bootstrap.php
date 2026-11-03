<?php
// Carregamento manual, sem Composer.
require_once __DIR__.'/Database.php';
require_once __DIR__.'/validacao.php';
foreach (['Usuario','Aluno','Plano'] as $nome) {
    require_once __DIR__.'/../models/'.$nome.'.php';
    require_once __DIR__.'/../controllers/'.$nome.'Controller.php';
}