<?php
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */
?>
<div class="modal fade in" id="modal-file-import" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Импорт номеров из файла</h4>
            </div>
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="filePhones">Файл с телефонами</label>
                    <input type="file" id="filePhones" name="file_phone">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>