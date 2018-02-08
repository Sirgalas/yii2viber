<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use common\entities\ViberMessage;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\entities\ViberMessage */
/* @var $form yii\widgets\ActiveForm */
/*  @var array $contact_collections */
/* @var array $assign_collections */
?>

    <div class="viber-message-form" style="margin-top: 20px">
        <div class="box box-solid box-default">
            <div class="box-header">
                <h3 class="box-title"> Viber Рассылка </h3>
            </div><!-- /.box-header -->
            <div class="box-body">

                <?php $form = ActiveForm::begin(); ?>
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

                                <?=$form->field($model, 'user_id')->textInput()?>

                            <?php endif ?>
                            <?=$form->field($model, 'status')->textInput(['maxlength' => true, 'disabled' => true])?>

                            <?=$form->field($model, 'limit_messages')->textInput()?>

                            <? //=$form->field($model, 'cost')->textInput()?>
                            <? //=$form->field($model, 'balance')->textInput()?>

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

                            <?=$form->field($model, 'image')->textInput(['maxlength' => true, 'id' => 'field_image'])?>

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
                                                 ]);
                            ?>
                            <button type="button" class="btn btn-block btn-primary btn-sm">Primary</button>
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
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        break;
                    case
                    '<?= ViberMessage::ONLYIMAGE?>'
                    :
                        $('.field-filed_text').hide();
                        $('.field-field_image').show();
                        $('.field-field_title_button').hide();
                        $('.field-field_url_button').hide();
                        break;
                    case
                    '<?= ViberMessage::TEXTBUTTON?>'
                    :
                        $('.field-filed_text').show();
                        $('.field-field_image').hide();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        break
                    case
                    '<?= ViberMessage::TEXTBUTTONIMAGE?>'
                    :
                        $('.field-filed_text').show();
                        $('.field-field_image').show();
                        $('.field-field_title_button').show();
                        $('.field-field_url_button').show();
                        break

                }
            }

            manageVisible();
            $('#field_type').change(manageVisible);
        }
    </script>
<?php
$js = '
  
       $(document).ready(function() {initPage();});
';
$this->registerJs($js);