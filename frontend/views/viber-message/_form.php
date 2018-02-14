<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use common\entities\ViberMessage;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\entities\ViberMessage */
/* @var $form yii\widgets\ActiveForm */
/*  @var array $contact_collections */
/* @var array $assign_collections */
?>
    <div class="viber-message-form" style="margin-top: 20px" data-id="<?= $model->id ?>">
        <div class="box box-solid box-default">
            <div class="box-header">
                <h3 class="box-title"> Viber Рассылка <span class="small">стоимость рассылки <span id="coast"><?= $model->cost ?></span> vib</span></h3>
            </div><!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
                <div class="col-md-5">
                    <div class="box box-solid box-default">
                        <div class="box-header">
                            <h4 class="box-title">Параметры </h4>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?=$form->field($model, 'title')->textInput(['maxlength' => true])?>
                            <?=$form->field($model, 'date_start')->widget(DateTimePicker::classname(), [
                                'options' => ['placeholder' => 'Enter event time ...'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                ],
                            ]);?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=$form->field($model, 'time_start')->input('time')?>
                                </div>
                                <div class="col-md-6">
                                    <?=$form->field($model, 'time_finish')->input('time')?>
                                </div>
                            </div>
                            <?=$form->field($model, 'date_finish')->widget(DateTimePicker::classname(), [
                                'options' => ['placeholder' => 'Enter event time ...'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                ],
                            ]);?>
                            <?php if (! Yii::$app->user->identity->isClient()): ?>
                                <?=$form->field($model, 'user_id')->widget(Select2::classname(), [
                                    'data' => $clients,
                                    'language' => 'ru',
                                    'options' => ['placeholder' => 'Выбирите клента'],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],

                                ]);?>
                            <?php endif ?>
                            <?=$form->field($model, 'status')->textInput(['maxlength' => true, 'disabled' => true])?>

                            <?=$form->field($model, 'limit_messages')->textInput()/*?>
                            <?=$form->field($model, 'cost')->textInput() ?>
                            <? =$form->field($model, 'balance')->textInput()*/?>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box box-solid box-default">
                        <div class="box-header">
                            <h4 class="box-title">Контент </h4>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?=$form->field($model, 'type')->dropDownList($model->listTypes(),
                                                                          ['maxlength' => true, 'id' => 'field_type'])?>

                            <?=$form->field($model, 'text')->textarea(['maxlength' => true, 'id' => 'filed_text'])?>

                            <img src="/uploads/<?= $model->image ?>" id="viber_image" style="max-width: 100%;max-height: 20vh;border: black solid 1px;">
                            <?= $form->field($model, 'upload_file')->fileInput(['maxlength' => true, 'id' => 'field_image']) ?>
                            <?=$form->field($model, 'title_button')->textInput([
                                                                                   'maxlength' => true,
                                                                                   'id' => 'field_title_button',
                                                                               ])?>

                            <?=$form->field($model, 'url_button')->textInput([
                                                                                 'maxlength' => true,
                                                                                 'id' => 'field_url_button',
                                                                             ])?>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="box box-solid box-default">
                        <div class="box-header">
                            <h4 class="box-title">Назначенные Коллекции </h4>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <?php
                            if ($model->isNewRecord){
                                echo '<h3>Будет доступно после сохранения рассылки</h3>';
                            }
                            else {
                                echo Select2::widget([
                                 'id' => 'contact_collections_field',
                                 'name' => 'contact_collection[id]',
                                 'value' => $assign_collections, // initial value
                                 'data' => $contact_collections,
                                 'maintainOrder' => true,
                                 'options' => [
                                     'placeholder' => 'Выберите коллекции ...',
                                     'multiple' => true,
                                 ],
                                 'pluginOptions' => [
                                     'tags' => true,
                                     'maximumInputLength' => 10,
                                 ],
                                "pluginEvents"=>[
                                    "change" => "function(e) {
                                        var cost = $(this).val(); 
                                        var id= $('.viber-message-form').data('id');                                                          
                                        $.ajax(
                                            {
                                                url: '/viber-message/coast',
                                                type: 'POST',
                                                data: {'data': cost,'id':id},
                                                success: function (data) {
                                                if(data){
                                                    $('#coast').html(data);
                                                    $('#assign_button').show();
                                                    }
                                                else{
                                                     alert('баланс пользователя не может быть отрицательным');
                                                     alert(cost);
                                                    if(cost.length>0)
                                                     $('#assign_button').hide();
                                                    }
                                                },
                                            });
                                    }",
                                    "select2:unselect" => "function(e) { 
                                        var cost = $(this).val(); 
                                        if(cost.length>0)
                                              $('#assign_button').hide();
                                    }"
                                ]
                             ]);
                            }
                            ?>
                            <button type="button" class="btn btn-block btn-primary btn-sm" style="margin: 20px auto" id="assign_button">Назначить</button>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <?=Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success'])?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
    <script>

        function initPage() {
            function manageVisible() {
                var type = $('#field_type').val();
                switch (type) {
                    case
                    '<?= ViberMessage::ONLYTEXT?>'
                    :
                        $('.field-filed_text').show();
                        $('.field-field_image').hide();
                        $('#viber_image').hide();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        break;
                    case
                    '<?= ViberMessage::ONLYIMAGE?>'
                    :
                        $('.field-filed_text').hide();
                        $('.field-field_image').show();
                        $('#viber_image').show();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        break;
                    case
                    '<?= ViberMessage::TEXTBUTTON?>'
                    :
                        $('.field-filed_text').show();
                        $('.field-field_image').hide();
                        $('#viber_image').hide();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        break
                    case
                    '<?= ViberMessage::TEXTBUTTONIMAGE?>'
                    :
                        $('.field-filed_text').show();
                        $('.field-field_image').show();
                        $('#viber_image').show();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        break
                }
            }
            manageVisible();
            $('#field_type').change(manageVisible);
            $('#assign_button').hide();
            $('#assign_button').click(function(){
                var data = $('#contact_collections_field').val();
                $.ajax(
                    {
                        url: "<?= Url::to(['viber-message/' .  $model->id . '/assign-collection' ])?>",
                        type: "POST",
                        data: {'data':data},
                        success: function(data){
                            if (data=='ok'){
                                $('#new_phones').val('');
                                $('#assign_button').hide();
                            } else {
                                alert(data);
                            }
                        },
                        error: function(data){
                            alert('Произошла ошика на сервере. Пожалуйста обратитесь к администратору.');
                        }
                    });
            });

            /**
             *
             */
            /*$('#contact_collections_field').change(function(){
                $('#assign_button').show();
            })*/
        }
    </script>
<?php
$js = '
  
       $(document).ready(function() {initPage();});
';
$this->registerJs($js);