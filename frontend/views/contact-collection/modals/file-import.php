<?php
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $modalForm frontend\forms\FileForm */
?>
<div class="modal fade in" id="modal-file-import" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Импорт номеров из файла</h4>
            </div>
            <?php $form = ActiveForm::begin(['action'=>Url::to('/contact-collection/import-file'),'options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="col-md-12 col-md-ofsset-1">
                <?= $form->field($modalForm,'file')->widget(FileInput::classname())->label(false); ?>
            </div>
            <div class="col-md-12 col-md-ofsset-1">
                <div class="col-md-6">
                    <?= $form->field($modalForm,'fieldPhone')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modalForm,'fieldUsername')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modalForm,'first_row')->textInput() ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modalForm,'delimiter')->textInput() ?>
                </div>
                    <?= $form->field($modalForm,'collection_id')->hiddenInput(['value'=>$model->id])->label(false) ?>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <?= Html::submitButton(Yii::t('front','Save'), ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>