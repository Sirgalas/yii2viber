<?php
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modalForm frontend\forms\FileForm */
?>
<div class="modal fade in" id="modal-collection-import" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Импорт номеров из файла</h4>
            </div>
            <?php $form = ActiveForm::begin(['action'=>Url::to('/contact-collection/import-collection'),'options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="col-md-12 col-md-ofsset-1">
                <?= $form->field($contactForm,'collection_id')->widget(Select2::class(), [
                    'data' => $contactCollection,
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выбирите коллекцию'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label(false); ?>
            </div>
            <?= $form->field($contactForm, 'some_collection')->hiddenInput(['value'=>$model->id])->label(false); ?>
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