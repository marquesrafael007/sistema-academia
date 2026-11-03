<?php
require __DIR__.'/bootstrap.php';
metodos(['GET']);
$planos=(new Plano())->listar();
foreach ($planos as &$plano) { $plano['id']=(int)$plano['id']; }
// Valor permanece string decimal para não perder precisão monetária.
unset($plano);
jsonResposta($planos);
