<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\entities\ViberMessage;
use common\entities\Provider;
use kartik\checkbox\CheckboxX;
use kartik\widgets\Select2;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;

use common\components\ViberIcons;

$listIcons=explode(',', ViberIcons::iconListAsString());
/* @var $this yii\web\View */
/* @var $model common\entities\ViberMessage */
/* @var $form yii\widgets\ActiveForm */
/*  @var array $contact_collections */
/* @var array $assign_collections */
$this->registerJsFile('/js/jquery.toggleinput.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('/css/jquery.toggleinput.css ');

?>


    <div class="viber-test-message-form row " data-id="<?=$model->id?>">
        <?php
        if ($model->status && !$model->isDeleteble() ) {
            ?>
            <div style="padding: 20px 30px; background: rgb(243, 156, 18); z-index: 999999; font-size: 16px; font-weight: 600;">
                Эта рассылка доступна только для просмотра.
                Обработка рассылки уже идет.
            </div>
        <?php      }

        if (Yii::$app->user->identity->isAdmin()){
            $form = ActiveForm::begin([ 'action' => 'moderate',
                'options' => ['enctype' => 'multipart/form-data'],
            ]);
                    ?>
            <div class="col-xs-12">
                <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
                <?php if ($model->status == ViberMessage::STATUS_CHECK && Yii::$app->user->identity->isAdmin() && $model->cost>0) {

                    ?>

                <div class="col-xs-3">
                    <input type="submit" class="btn btn-block btn-success btn-lg" id="moderation_on" name="allow"
                           value="Одобрить">
                </div>
                <div class="col-xs-3">
                    <input type="submit" class="btn btn-block btn-warning btn-lg" id="moderation_cancel"
                           value="Отправить на доработку" name="disallow">
                </div>
                <?php } ?>
                <div class="col-xs-3 pull-right">
                    <input type="submit" class="btn btn-block btn-danger btn-lg" id="close"
                           value="Закрыть рассылку" name="close">
                </div>

            </div>
            <?php ActiveForm::end();
        }
        if ( Yii::$app->user->identity->isAdmin() && $model->cost == 0) { ?>
            <div style="padding: 20px 30px; background: rgb(243,223,34); z-index: 999999; font-size: 16px; font-weight: 600;">
                Эта рассылка доступна только для просмотра.
                Не назначены реальные телефоны
            </div>
        <?php } ?>
        <div class="col-xs-12">
            <div class="col-xs-12" class="small">
                Cтоимость рассылки <span class="small" id="cost"><?=number_format($model->cost)?></span> SMS
            </div>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'enableClientValidation'=>false]); ?>
        <div class="col-md-7">
            <div class="col-md-7">
                <div class="block-header">
                    Рассылка
                </div>
                <?= $form->field($model, 'channel')
                    ->dropDownList(array_combine(Yii::$app->params['channels'],Yii::$app->params['channels']),
                        ['maxlength' => true, 'id' => 'field_channel']) ?>
                <?=$form->field($model, 'type')->dropDownList(ViberMessage::listTypes(),
                    ['maxlength' => true, 'id' => 'field_type'])?>
                <div class="form-group radio-toggle" style="display: none">
                    <label class="control-label" for="field_type">Назначение сообщения</label>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ViberMessage[message_type]"
                                   id="exampleRadios1"
                                   value="Реклама" <?=$model->message_type != 'Информация' ? 'checked' : ''?>>
                            Реклама
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input" type="radio" name="ViberMessage[message_type]"
                                   id="exampleRadios2"
                                   value="Информация" <?=$model->message_type == 'Информация' ? 'checked' : ''?>>
                            Информация
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-md-5" style="  z-index: 9999;text-align: center;">
                <div class="block-header">&nbsp;</div>
                <div id="smiles_block" style="width: 100%;height:205px;overflow: auto;">
                    <?php
                    foreach ($listIcons as $icon){
                        echo "<img class='viber-icon' data-text='$icon' title='$icon' src='" .  ViberIcons::ICON_PATH  . $icon. ".png'>";
                    }
                    ?>
                    <style>
                        .viber-icon {
                            max-width: 25px;max-height: 25px;margin: 2px;cursor: pointer;
                        }
                    </style>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 20px;">
                <div style="position: relative;">
                    <?=$form->field($model, 'text')->textarea([
                        'maxlength' => true,
                        'id' => 'filed_text',
                        'rows' => 10,
                    ])?>


                    <div id="remaining_text"></div>
                </div>
                <?php if ($model->image) : ?>
                    <img src="<?=$model->image?>" id="viber_image"
                         style="max-width: 100%;max-height: 20vh;border: black solid 1px;">
                <?php endif ?>
                <?=$form->field($model, 'upload_file')->fileInput(['maxlength' => true, 'id' => 'field_image'])?>
                <?php if ($model->hasErrors('image')) : ?>
                    <div style="color: #b66161;margin-top: -10px;margin-bottom: 15px;"><?=
                        implode ('<br>',$model->getErrors('image')) ?></div>
                <?php endif ?>
                <?=$form->field($model, 'title_button')->textInput([
                    'maxlength' => true,
                    'id' => 'field_title_button',
                ])?>

                <?=$form->field($model, 'url_button')->textInput([
                    'maxlength' => true,
                    'id' => 'field_url_button',
                ])?>

                <?=$form->field($model, 'image_caption')->textarea([
                    'maxlength' => true,
                    'id' => 'field_image_caption',
                    'rows' => 10,
                ])?>
            </div>
            <div class="form-group col-md-12">
                <?php if (!$model->status || $model->isEditable()) {

                    echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary right-20',  'name'=>'button' ,'value'=>'save' ]);
                    echo '<span style="width:20px"></span>';

                    echo Html::submitButton('Отправить на модерацию', ['class' => 'btn btn-success right-20',  'name'=>'button' ,'value'=>'check']);
                }
                if ($model->status && $model->isCancalable()) {
                    echo Html::submitButton('Прервать', ['class' => 'btn btn-primary right-20',   'name'=>'button' ,'value'=>'cancel']);
                }
                ?>





            </div>
        </div>


        <div class="col-md-5">
            <div class="block-header">Задать параметры рассылки</div>
            <?=$form->field($model, 'title')->textInput([
                'maxlength' => true,
            ])?>
            <?=$form->field($model, 'alpha_name')->dropDownList(Provider::getAlphaNames($model->defineProvider()),
                ['maxlength' => true,])?>
            <?php

            echo $form->field($model, 'assign_collections')->widget(Select2::class, [
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
                        var channel= $('#field_channel').val();
                        $.ajax(
                            {
                                url: '/viber-message/cost',
                                type: 'POST',
                                dataType: 'json',
                                data: {'data': cost,'id':id, 'channel':channel},
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

            ?>
            <?=$form->field($model, 'date_start')->widget(DatePicker::class, [
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
                <?=$form->field($model, 'just_now')->widget(CheckboxX::class, [
                    'pluginOptions' => ['threeState' => false, 'size' => 'lg', 'class' => 'has-sucess'],
                ])->label(false)?>
            </div>
            <?=$form->field($model, 'dlr_timeout')->textInput([
                'maxlength' => true,
            ])?>
            <div style="font-size: 0.4">
                Минимальное значение – 60 (одна минута).

                Максимальное значение – 86400 (24 часа).

                Значение округляется до минут, в меньшую сторону.

                В случае, если не указано, считается, что равен 14 дням.

                Этот параметр используется для того, чтобы оперативно отправлять сообщения по каналу, отличному от Viber
                (SMS, USSD или подобное)
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <script>
        var text_size_limit = 1000;
        function calcRemaining(obj, maxCount) {
            var val = ($(obj).val());
            if (val.length > maxCount) {
                val = val.substr(0, maxCount);
                $(obj).val(val);

            }
            var remaining = maxCount - val.length;
            return '' + remaining + ' символов осталось';
        }

        function informToptext(obj) {
            var txt = calcRemaining(obj, text_size_limit);
            $('#remaining_text').html(txt);
        }

        function initPage() {

            informToptext($('#filed_text')[0]);
            $('#filed_text').keyup(function () {
                informToptext(this);
            })
            $('.radio-toggle').toggleInput();
            $('.radio-toggle').show();
            $('.viber-icon').click(function(){
                var code = $(this).attr('data-text');
                var fild_text =  $('#filed_text');
                var txt = fild_text.val();
                fild_text.val(txt + '(' + code + ')');
                informToptext(fild_text);
            });
            function manageChannelVisible(){
                var type = $('#field_channel').val();
                switch (type){
                    case 'viber':
                        $('#field_image_caption').hide();
                        $('#smiles_block').show();
                        $('.field-field_type').show();
                        $('.field-vibermessage-alpha_name').show();

                        manageVisible();
                        break;
                    case 'whatsapp':
                        $('.field-filed_text').show();
                        $('#remaining_text').show();
                        $('.field-field_image').show();
                        $('#viber_image').show();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        $('.field-field_type').hide();
                        $('.field-vibermessage-alpha_name').hide();
                        $('.field-field_image_caption').hide();
                        $('#smiles_block').hide();
                        $('#field_image_caption').show();
                        break;
                    case 'sms':
                        $('.field-filed_text').show();
                        $('#remaining_text').show();
                        $('.field-field_image').hide();
                        $('#viber_image').hide();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        $('.field-field_type').hide();
                        $('.field-field_image_caption').hide();
                        $('.field-vibermessage-alpha_name').hide();
                        $('#smiles_block').hide();
                        $('#field_image_caption').show();
                        break;
                }
            }
            function manageVisible() {

                var type = $('#field_type').val();
                switch (type) {
                    case
                    '<?= ViberMessage::ONLYTEXT?>'
                    :
                        $('.field-filed_text').show();
                        $('#remaining_text').show();
                        $('.field-field_image').hide();
                        $('#viber_image').hide();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        $('.field-field_image_caption').hide();
                        break;
                    case
                    '<?= ViberMessage::ONLYIMAGE?>'
                    :
                        $('.field-filed_text').hide();
                        $('#remaining_text').hide();
                        $('.field-field_image').show();
                        $('#viber_image').show();
                        $('.field-field_image_caption').show();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        break;
                    case
                    '<?= ViberMessage::TEXTBUTTON?>'
                    :
                        $('.field-filed_text').show();
                        $('#remaining_text').show();
                        $('.field-field_image').hide();
                        $('#viber_image').hide();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        $('.field-field_image_caption').hide();
                        break
                    case
                    '<?= ViberMessage::TEXTBUTTONIMAGE?>'
                    :
                        $('.field-filed_text').show();
                        $('#remaining_text').show();
                        $('.field-field_image').show();
                        $('#viber_image').show();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        $('.field-field_image_caption').show();
                        break
                }
            }

            manageChannelVisible();
            $('#field_type').change(manageVisible);
            $('#field_channel').change(manageChannelVisible);

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