<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$link = Yii::$app->urlManager->createAbsoluteUrl(['/client/update', 'id' => $user->id]);
?>
Новый пользователь зарегистрировался

<?= $link ?>
