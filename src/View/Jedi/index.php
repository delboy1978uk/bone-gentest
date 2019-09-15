<?php
use Del\Icon;
?>
<a href="/jedi/create" class="btn btn-default pull-right"><?= Icon::ADD ;?> Add a Jedi</a>
<h1>Jedi Admin</h1>
<?= $paginator ?>
<table class="table table-condensed table-bordered">
    <thead>
        <tr>
            <td>Id</td>
            <td>Name</td>
            <td>Edit</td>
            <td>Delete</td>
        </tr>
    </thead>
    <tbody>
    <?php
    /** @var \BoneMvc\Module\Jedi\Entity\Jedi $jedi */
    foreach ($jedis as $jedi) { ?>
        <tr>
            <td><a href="/jedi/<?= $jedi->getId() ?>"><?= $jedi->getId() ;?></a></td>
            <td><?= $jedi->getName() ;?></td>
            <td><a href="/jedi/edit/<?= $jedi->getId() ?>"><?= Icon::EDIT ;?></a></td>
            <td><a href="/jedi/delete/<?= $jedi->getId() ?>"><?= Icon::REMOVE ;?></a></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
