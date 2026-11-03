<?php if ($erros): ?>
<div class="alert alert-danger" role="alert"><ul class="mb-0">
<?php foreach ($erros as $erro): ?><li><?= e($erro) ?></li><?php endforeach; ?>
</ul></div><?php endif; ?>
