<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\ScenarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scenario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'provider') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'from1') ?>

    <?= $form->field($model, 'channel1') ?>

    <?php // echo $form->field($model, 'from2') ?>

    <?php // echo $form->field($model, 'channel2') ?>

    <?php // echo $form->field($model, 'from3') ?>

    <?php // echo $form->field($model, 'channel3') ?>

    <?php // echo $form->field($model, 'default')->checkbox() ?>

    <?php // echo $form->field($model, 'provider_scenario_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
