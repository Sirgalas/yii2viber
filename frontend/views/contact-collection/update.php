<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */
/* @var $phoneSearchModel common\entities\PhoneSearch */
/* @var $phoneDataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Update Contact Collection: {nameAttribute}', [
    'nameAttribute' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact Collections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
    <div class="contact-collection-update">


        <?=$this->render('_form', compact('model', 'phoneSearchModel', 'phoneDataProvider'))?>

        <div class="contact-collection-form col-md-8">
            <div class="box box-solid box-default">
                <div class="box-header">
                    <h3 class="box-title"> Телефоны </h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-7">
                            <?php echo $this->render('phone_grid/index',
                                                     compact('phoneSearchModel', 'phoneDataProvider')); ?>
                        </div>
                        <div class="col-md-5">
                            <a class="btn btn-app" id="remove_selected" title="Удалить выбранные">
                                <i class="fa fa-remove"></i> Удалить
                            </a>
                            <a class="btn btn-app" title="Оставить только выбранные">
                                <i class="fa fa-edit" id="use_only_selected"></i> Использовать
                            </a>
                            <a class="btn btn-app" title="Импорт из файла"   data-toggle="modal" data-target="#modal-file-import">
                                <i class="fa fa-download" id="import_from_file"></i>Импорт
                            </a>
                            <a class="btn btn-app" title="Импорт  других коллекций" data-toggle="modal" data-target="#modal-collection-import">
                                <i class="fa fa-clone" id="import_from_other"  ></i>Импорт
                            </a>
                            <div class="contact-collection-form ">
                                <div class="box box-solid box-default">
                                    <div class="box-header">
                                        <h3 class="box-title"> Новые телефоны </h3>
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
<!--                                        <button type="button" class="btn btn-block btn-primary btn-sm"-->
<!--                                                id="btn_from_clipboard">Заполнить из буфера обмена-->
<!--                                        </button>-->

                                        <textarea name="new_phones"
                                                  style="margin:10px auto; min-height: 5vh; width: 100%"
                                                  id="new_phones"></textarea>

                                        <button type="button" class="btn btn-block btn-sucess btn-sm" id="btn_save">
                                            Сохранить новые
                                        </button>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div>
        </div
    </div>
<?php echo $this->render('modals/file-import',
                         compact('modalForm','model')); ?>
<?= $this->render('modals/collection-import',compact('contactCollection','contactForm','model')) ?>
    <script>
        function reloadGridPhone(){
            $.pjax.reload({container: "#pjax-grid-view", async:false});
        }
        function initPage() {
           $('#btn_from_clipboard').click(function(){

           });
           $('#btn_save').click(function(){
                var txt=$('#new_phones').val();
                if (txt.length) {
                    $.ajax(
                        {
                            url: "<?= Url::to(['contact-collection/' .  $model->id . '/new-phones' ])?>",
                            type: "POST",
                            data: {'txt':txt},
                            success: function(data){
                                if (data=='ok'){
                                    $('#new_phones').val('');
                                    reloadGridPhone();
                                } else {
                                    alert(data);
                                }
                            }
                        });
                }
            });
            $('#remove_selected').click(function(){
                var lst=$('input[name="selection[]"]:checked');
                var ids=[];
                lst.each(function(){
                    ids.push($(this).closest('tr').attr('data-key'));
                });
                if (ids.length) {
                    $.ajax(
                        {
                            url: "<?= Url::to(['contact-collection/' .  $model->id . '/remove-phones' ])?>",
                            type: "POST",
                            data: {'ids':ids},
                            success: function(data){
                                if (data=='ok'){

                                    reloadGridPhone();
                                } else {
                                    alert(data);
                                }
                            }
                        });
                }
            });
            $('#use_only_selected').click(function(){
                var lst=$('input[name="selection[]"]:not(:checked)');
                var ids=[];
                lst.each(function(){
                    ids.push($(this).closest('tr').attr('data-key'));
                });
                if (ids.length) {
                    $.ajax(
                        {
                            url: "<?= Url::to(['contact-collection/' .  $model->id . '/remove-phones' ])?>",
                            type: "POST",
                            data: {'ids':ids},
                            success: function(data){
                                if (data=='ok'){

                                    reloadGridPhone();
                                } else {
                                    alert(data);
                                }
                            }
                        });
                }
            });
        }
    </script>
<?php
$js = '
  
       $(document).ready(function() {initPage();});
';
$this->registerJs($js);