<?php

/* @var $this yii\web\View */
/* @var $model frontend\forms\ViberTestForm */
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Поздравляем!</h1>
        <p class="lead">Вы успешно создали первую рассылку
            Вы можете увидеть детали вашей расылки <a href="/viber-message/update?id=<?= $model->viber_message_id ?>">здесь</a>
        </p>
    </div>
</div>
