<?php require __DIR__.'/partials/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card card-body shadow-sm">
<h1 class="h3">Entrar na academia</h1>
<form method="post" action="index.php?controller=usuario&acao=login">
<?php csrfInput(); ?>
<label for="email" class="form-label">E-mail</label><input class="form-control mb-3" type="email" id="email" name="email" autocomplete="username" required>
<label for="senha" class="form-label">Senha</label><input class="form-control mb-3" type="password" id="senha" name="senha" autocomplete="current-password" required>
<button class="btn btn-primary w-100">Entrar</button></form></div></div></div>
<?php require __DIR__.'/partials/footer.php'; ?>
