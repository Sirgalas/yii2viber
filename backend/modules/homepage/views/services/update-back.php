<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\Config */

$this->title = 'Редактировать предоставляемые сервисы: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_back', [
        'model' => $model,
    ]) ?>

</div>