<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\user\User */
/* @var $form yii\widgets\ActiveForm */
/* @var $dealers array */

$roles = ['client'=>'client','dealer'=>'dealer'];
if (Yii::$app->user->identity->isAdmin()){
    $roles['admin']='admin';
}
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($roles,['maxlength' => true]) ?>
    <?php if ($model->isNewRecord){
        $form->field($model, 'password')->textInput(['maxlength' => true]);
    }?>

    <?= $form->field($model, 'dealer_id')->dropDownList($dealers) ?>

    <?= $form->field($model, 'dealer_confirmed')->checkbox() ?>




    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
