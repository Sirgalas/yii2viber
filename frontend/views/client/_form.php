<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\user\User */
/* @var $balance common\entities\Balance */
/* @var $form yii\widgets\ActiveForm */
/* @var $dealers array */

$roles = ['client' => 'client', 'dealer' => 'dealer'];
if (Yii::$app->user->identity->isAdmin()) {
    $roles['admin'] = 'admin';
}
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-6">
        <h2>Пользователь</h2>
        <?=$form->field($model, 'username')->textInput(['maxlength' => true])?>

        <?=$form->field($model, 'email')->textInput(['maxlength' => true])?>

        <?=$form->field($model, 'type')->dropDownList($roles, ['maxlength' => true])?>
        <?php if ($model->isNewRecord) {
            $form->field($model, 'password')->textInput(['maxlength' => true]);
        } ?>

        <?=$form->field($model, 'dealer_id')->dropDownList($dealers)?>
        <?=$form->field($model, 'viber_provider')->dropDownList(array_combine(Yii::$app->params['providers'],
                                                                              Yii::$app->params['providers']))?>

        <?=$form->field($model, 'dealer_confirmed')->checkbox()?>

    </div>
    <div class="col-md-6">
        <h2>Баланс</h2>

        <?=$form->field($balance, 'viber_price')->textInput()?>
        <?=$form->field($balance, 'viber')->textInput(['maxlength' => false])?>


        <?=$form->field($balance, 'whatsapp_price')->textInput()?>
        <?=$form->field($balance, 'whatsapp')->textInput()?>




    </div>
    <div class="col-md-12">
    <div class="form-group">
        <?=Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success'])?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
