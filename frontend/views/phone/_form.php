<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-collection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
            'mask' => '+9(999)-999-99-99',
            'clientOptions'=>[
                'removeMaskOnSubmit' => true,
                'clearIncomplete'=>true
            ]
    ]) ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>   
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
