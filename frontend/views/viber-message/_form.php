<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\entities\ViberMessage $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="viber-message-form">

    <?php $form = ActiveForm::begin(['type' => ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

        'model' => $model,
        'form' => $form,
        'columns' => 1,
        'attributes' => [

            'user_id' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter User ID...']],

            'date_start' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Date Start...']],

            'date_finish' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Date Finish...']],

            'limit_messages' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Limit Messages...']],

            'title' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Title...', 'maxlength' => 50]],

            'text' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Text...', 'maxlength' => 120]],

            'image' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Image...', 'maxlength' => 255]],

            'title_button' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Title Button...', 'maxlength' => 32]],

            'url_button' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Url Button...', 'maxlength' => 255]],

            'cost' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Cost...']],

            'balance' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Balance...']],

            'alpha_name' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Alpha Name...', 'maxlength' => 32]],

            'type' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Type...', 'maxlength' => 10]],

            'time_start' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Time Start...', 'maxlength' => 5]],

            'time_finish' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Time Finish...', 'maxlength' => 5]],

            'status' => ['type' => Form::INPUT_TEXT, 'options' => ['placeholder' => 'Enter Status...', 'maxlength' => 16]],

        ]

    ]);

    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    );
    ActiveForm::end(); ?>

</div>
