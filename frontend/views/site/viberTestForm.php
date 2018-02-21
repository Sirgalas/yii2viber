<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\entities\ViberMessage;
use kartik\checkbox\CheckboxX;
use kartik\widgets\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\forms\ViberTestForm */
/* @var $form yii\widgets\ActiveForm */

?>
    <div class="viber-test-message-form row ">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="col-md-7">
            <div class="col-md-7">
                <div class="block-header">Тестовая рассылка</div>
                <?=$form->field($model, 'type')->dropDownList(ViberMessage::listTypes(),
                    ['maxlength' => true, 'id' => 'field_type'])?>
            </div>
            <div class="col-md-5" style="    z-index: 9999;">
                <div class="block-header">Введите номер</div>
                <?=$form->field($model, 'phone1')->textInput([
                    'maxlength' => true,
                    'id' => 'field_phone1',
                    'class' => 'labelLess form-control',
                ])->label(false)?>

                <?=$form->field($model, 'phone2')->textInput([
                    'maxlength' => true,
                    'id' => 'field_phone2',
                    'class' => 'labelLess form-control',
                ])->label(false)?>

                <?=$form->field($model, 'phone3')->textInput([
                    'maxlength' => true,
                    'id' => 'field_phone2',
                    'class' => 'labelLess form-control',
                ])->label(false)?>
            </div>
            <div class="col-md-12" style="margin-top:-55px">
                <div style="position: relative;">
                    <?=$form->field($model, 'text')->textarea([
                        'maxlength' => true,
                        'id' => 'filed_text',
                        'rows' => 10,
                    ])?>
                    <div id="remaining_text"></div>
                </div>
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
            $items = ViberMessage::getAlphaNames();
            $options = [];
            foreach ($items as $key => $val) {
                if ($key != 'Clickbonus') {
                    $options[$key] = ['disabled' => true];
                }
            }
            echo $form->field($model, 'alpha_name')->dropDownList($items, ['maxlength' => true, 'options' => $options]);
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
            var txt = calcRemaining(obj, 1000);
            $('#remaining_text').html(txt);
        }

        function initPage() {

            informToptext($('#filed_text')[0]);
            $('#filed_text').keyup(function () {
                informToptext(this);
            })

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