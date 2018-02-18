<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\entities\ViberMessage;
use kartik\checkbox\CheckboxX;
use kartik\widgets\Select2;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\entities\ViberMessage */
/* @var $form yii\widgets\ActiveForm */
/*  @var array $contact_collections */
/* @var array $assign_collections */
?>

    <div class="viber-test-message-form row " data-id="<?=$model->id?>">
        <div class="col-xs-12">
            <div class="col-xs-12" class="small">
                Cтоимость рассылки <span class="small" id="cost"><?=number_format($model->cost)?></span> SMS
            </div>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="col-md-7">
            <div class="col-md-7">
                <div class="block-header">
                    Рассылка

                </div>
                <?=$form->field($model, 'type')->dropDownList(ViberMessage::listTypes(),
                    ['maxlength' => true, 'id' => 'field_type'])?>
            </div>
            <div class="col-md-5" style="  z-index: 9999;text-align: center;">
                <div class="block-header">&nbsp;</div>
                <a href="https://hyperhost.ua/ru" target="_blank"><img src="/images/banner.png" style="margin: 10px auto;   "></a>
            </div>
            <div class="col-md-12" style="margin-top:-55px">
                <?=$form->field($model, 'text')->textarea(['maxlength' => true, 'id' => 'filed_text', 'rows' => 10])?>

                <?php if ($model->image) : ?>
                    <img src="/uploads/<?=$model->image?>" id="viber_image"
                         style="max-width: 100%;max-height: 20vh;border: black solid 1px;">
                <?php endif ?>
                <?=$form->field($model, 'upload_file')->fileInput(['maxlength' => true, 'id' => 'field_image'])?>
                <?=$form->field($model, 'title_button')->textInput([
                    'maxlength' => true,
                    'id' => 'field_title_button',
                ])?>

                <?=$form->field($model, 'url_button')->textInput([
                    'maxlength' => true,
                    'id' => 'field_url_button',
                ])?>
            </div>
            <div class="form-group col-md-12">
                <?=Html::submitButton('Отправить', ['class' => 'btn btn-success'])?>
            </div>
        </div>


        <div class="col-md-5">
            <div class="block-header">Задать параметры рассылки</div>
            <?=$form->field($model, 'title')->textInput([
                'maxlength' => true,
            ])?>
            <?php
            if ($model->isNewRecord){
                echo '<h3>Будет доступно после сохранения рассылки</h3>';
            } else {
                echo $form->field($model, 'assign_collections')->widget(Select2::classname(), [
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
                    "pluginEvents" => [
                        "change" => "function(e) {
                        var cost = $(this).val();
                        var id= $('.viber-test-message-form').data('id');
                        $.ajax(
                            {
                                url: '/viber-message/cost',
                                type: 'POST',
                                dataType: 'json',
                                data: {'data': cost,'id':id},
                                success: function (data) {
                                    if(data){
                                        if(data.result == 'ok'){ 
                                            $('#cost').html(data.cost);
                                            if (data.balance <0){
                                                $('#cost').css('color','red');
                                                $('.field-vibermessage-assign_collections').addClass('has-error');
                                                $('.field-vibermessage-assign_collections .help-block').html('Недостаточно средств на балансе');
                                                
                                            } else {
                                                $('#cost').css('color','rgb(51, 51, 51)');
                                                 $('.field-vibermessage-assign_collections').removeClass('has-error');
                                                  $('.field-vibermessage-assign_collections .help-block').html('');
                                            }
                                        }                               
                                    }
                                    else{
                                        alert('баланс пользователя не может быть отрицательным');
                                        $('#assign_button').hide();
                                    }
                                },
                            });
                    }",
                    ],
                ]);
            }
            ?>
            <?=$form->field($model, 'date_start')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Дата отправки'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
            ]);?>
            <div style="width: 48%;float:left;">
                <?=$form->field($model, 'time_start')->input('time')?>

            </div>
            <div style="width: 48%;float: right" class="has-success">
                <label class="cbx-label" style="margin-bottom: 5px;">Прямо сейчас</label>
                <?=$form->field($model, 'just_now')->widget(CheckboxX::classname(), [
                    'pluginOptions' => ['threeState' => false, 'size' => 'lg', 'class' => 'has-sucess'],
                ])->label(false)?>
            </div>

        </div>

        <?php ActiveForm::end(); ?>
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