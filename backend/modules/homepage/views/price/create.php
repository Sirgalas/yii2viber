<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\Config */

$this->title = 'Создать цены';
$this->params['breadcrumbs'][] = ['label' => 'Цены', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
