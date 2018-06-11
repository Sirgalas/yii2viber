<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\checkbox\CheckboxX;
/**
* @var $this yii\web\View
* @var $searchModel common\entities\ViberTransaction
* @var $dataProvider yii\data\ActiveDataProvider
* @var $model common\entities\ViberTransaction
 * @var $messagePhoneList common\entities\mongo\Message_Phone_List
 * @var array $status
 **/
$this->title = Yii::t('app', 'Статистика');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="statistics-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <div class="col-md-12">
        <div class="col-md-10 col-md-offset-1">
            <?php $form=ActiveForm::begin(); ?>
            <?= $form->field($model,'titleSearch')->textInput(['placeholder'=>'Выберите телефон или название рассылки'])->label(false) ?>
            <div class="col-md-5 col-md-offset-1">
                <div class="form-group">
                    <?= DatePicker::widget([
                        'value' => $post['dateFrom'],
                        'value2' => $post['dateTo'],
                        'name' => 'ViberTransaction[dateFrom]',
                        'type' => DatePicker::TYPE_RANGE,
                        'name2' => 'ViberTransaction[dateTo]',
                        'layout' => '<span class="input-group-addon"> С </span>{input1}<span class="input-group-addon"> по </span>{input2}',
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ]);?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'contactCollection')->widget(Select2::class, [
                        'data' => $contact_collections,
                        'maintainOrder' => true,
                        'options' => [
                        'placeholder' => 'Выберите коллекции ...',
                        ],
                    ])->label(false); ?>
                </div>
                <div class="form-group">
                    <?php if(!Yii::$app->user->identity->isClient()){
                        echo $form->field($model, 'user_id')->widget(Select2::class, [
                            'data' => $clients,
                            'maintainOrder' => true,
                            'options' => [
                                'placeholder' => 'Выберите пользователя ...',
                            ],
                        ])->label(false);
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group ">
                    <?= $form->field($model, 'status')->checkboxList($status)->label('Статусы');?>
                </div>
            </div>
            <div class="form-group col-md-12 bottom-center">
                <?= Html::submitButton("<i class='glyphicon glyphicon-search'></i>Поиск", ['class' => 'btn btn-success center']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="col-md-12">
        <?php
            echo $this->render('_mongo',compact('dataProvider','searchModel','exeportExelDataProvider','post','providerFromGetModel'));
       ?>
    </div>
    <div class="clearfix"></div>
    <?php Pjax::end(); ?>
</div>
