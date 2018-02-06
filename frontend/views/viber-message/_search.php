<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\entities\ViberMessageSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="viber-message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'text') ?>

    <?= $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'title_button') ?>

    <?php // echo $form->field($model, 'url_button') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'alpha_name') ?>

    <?php // echo $form->field($model, 'date_start') ?>

    <?php // echo $form->field($model, 'date_finish') ?>

    <?php // echo $form->field($model, 'time_start') ?>

    <?php // echo $form->field($model, 'time_finish') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'limit_messages') ?>

    <?php // echo $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
