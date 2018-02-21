<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\entities\user\User */
/* @var $dealers array */

$this->title = Yii::t('app', 'Изменить: {nameAttribute}', [
    'nameAttribute' => $model->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Профиль'), 'url' => ['views']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изминить');
?>
<div class="profile-update">
    <div class="profile-form" style="margin-top: 20px">
        <div class="box-header">
            <h3 class="box-title"><?=Html::encode($this->title)?></h3>
        </div
        <div class="box-body">
            <div class="profile-form">

                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'tel')->textInput(['maxlength' => true])->label('Телефон') ?>

                <?= $form->field($model, 'family')->textInput(['maxlength' => true])->label('Фамилия') ?>

                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true])->label('Имя') ?>

                <?= $form->field($model, 'surname')->textInput(['maxlength' => true])->label('Отчество') ?>



                <?= $form->field($model, 'time_work')->textInput(['maxlength' => true])->label('Время работы') ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>