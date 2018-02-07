<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */

$this->title = Yii::t('app', 'Create Contact Collection');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact Collections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-collection-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
