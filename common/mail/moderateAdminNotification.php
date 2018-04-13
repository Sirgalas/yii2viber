<?php

/* @var $this yii\web\View */
/* @var $message common\models\ViberMessage */

$link = Yii::$app->urlManager->createAbsoluteUrl(['viber-message/update', 'id' => $message->id]);
?>

Нужна модерация рассылки:: <?= $message->title ?>

<a href="<?= $link ?>"><?= $link ?></a>
