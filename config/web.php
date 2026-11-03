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
