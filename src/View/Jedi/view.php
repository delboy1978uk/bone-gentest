<?php
use Del\Icon;

/** @var \BoneMvc\Module\Jedi\Entity\Jedi $jedi */
?>
<a href="/jedi" class="btn btn-default pull-right"><?= Icon::CARET_LEFT ;?> Back</a>

<h1>View Jedi</h1>
<div class="">
    <h2><?= $jedi->getName() ?></h2>
</div>
<a href="/jedi/edit/<?= $jedi->getId() ?>" class="btn btn-default">
    <?= Icon::EDIT ;?> Edit
</a>
