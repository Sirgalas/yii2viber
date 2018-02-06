<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\mongo\Phone */

$this->title = Yii::t('app', 'Update Contact Collection: {nameAttribute}', [
    'nameAttribute' => $model->phone,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact Collections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->phone, 'url' => ['view', 'id' => (string)$model['_id']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="contact-collection-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'entities'=>$entities
    ]) ?>

</div>
