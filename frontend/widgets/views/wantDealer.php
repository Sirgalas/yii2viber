<?php
use yii\helpers\Url;
?>
<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-envelope-o"></i>
        <span class="label label-success"><?= count($users); ?></span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">У вас <?= count($users); ?> клиентов хотят стать дилерами </li>
        <li>
            <ul class="menu">
                <?php foreach ($users as $user){ ?>
                    <li>
                        <a href="<?= Url::to(['/client/update','id'=>$user->id])?>">

                            <p> Ваш клиент <?= $user->username; ?> хочет стать дилером</p>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
        <li class="footer"><a href="<?= Url::to('/client/index')?>">Смотреть всех клиентов</a></li>
    </ul>
</li>