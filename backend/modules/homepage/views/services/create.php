<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\Config */

$this->title = 'Создать сервисы';
$this->params['breadcrumbs'][] = ['label' => 'Предоставляемые сервисы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
