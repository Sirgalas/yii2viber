<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */
/* @var $form yii\widgets\ActiveForm */
/* @var $phoneSearchModel common\entities\PhoneSearch */
/* @var $phoneDataProvider yii\data\ActiveDataProvider */

?>

<div class="contact-collection-form col-md-4">
    <div class="box box-solid box-default">
        <div class="box-header">
            <h3 class="box-title"> <?=Html::encode($this->title)?></h3>
        </div><!-- /.box-header -->
        <div class="box-body">

            <?php $form = ActiveForm::begin(); ?>
            <?=$form->field($model, 'title')->textInput(['maxlength' => true])?>
            <div class="form-group">
                <?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success'])?>
            </div>

            <?php ActiveForm::end(); ?>

        </div><!-- /.box-body -->
    </div>
</div>
