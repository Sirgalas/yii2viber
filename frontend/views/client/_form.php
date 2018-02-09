<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\user\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $dealers array */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(['client','dealer'],['maxlength' => true]) ?>
    <?php if ($model->isNewRecord){
        $form->field($model, 'password')->textInput(['maxlength' => true]);
    }?>

    <?= $form->field($model, 'dealer_id')->dropDownList($dealers) ?>

    <?//= $form->field($model, 'balance')->textInput() ?>

    <?//= $form->field($model, 'confirmed_at')->textInput() ?>

    <?= $form->field($model, 'dealer_confirmed')->checkbox() ?>

    <?//= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>