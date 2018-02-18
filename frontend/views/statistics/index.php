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
 **/


$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <div class="col-md-10 col-md-offset-1">
        <?php $form=ActiveForm::begin(); ?>
        <?= $form->field($model,'titleSearch')->textInput(['placeholder'=>'Выбирите телефон или название рассылки'])->label(false) ?>
        <div class="col-md-5 col-md-offset-1">
            <div class="form-group">
                <?= DatePicker::widget([
                    'name' => 'ViberTransaction[dateFrom]',
                    'value' => date('d-M-Y',time()),
                    'type' => DatePicker::TYPE_RANGE,
                    'name2' => 'ViberTransaction[dateTo]',
                    'value2' => date('d-M-Y',time()),
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-M-yyyy'
                    ]
                ]);?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'contactCollection')->widget(Select2::classname(), [
                    'data' => $contact_collections,
                    'maintainOrder' => true,
                    'options' => [
                    'placeholder' => 'Выберите коллекции ...',
                    ],
                ])->label(false); ?>
            </div>
            <div class="form-group">
                <?php if(!Yii::$app->user->identity->isClient()){
                    echo $form->field($model, 'user_id')->widget(Select2::classname(), [
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
            <div class="form-group">
                <?php echo '<label class="cbx-label col-md-5" for="s_1">Новое</label>';
                echo CheckboxX::widget([
                    'name'=>'ViberTransaction[status][]',
                    'options'=>['id'=>'s_1'],
                    'value'=>$messagePhoneList::NEWMESSAGE,
                    'pluginOptions'=>['threeState'=>false]
                ]); ?>
            </div>
            <div class="form-group">
                <?php echo '<label class="cbx-label col-md-5" for="s_2">Отправлено</label>';
                echo CheckboxX::widget([
                'name'=>'ViberTransaction[status][]',
                'options'=>['id'=>'s_2'],
                 'value'=>$messagePhoneList::SENDED,
                'pluginOptions'=>['threeState'=>false]
                ]); ?>
            </div>
            <div class="form-group">
                <?php echo '<label class="cbx-label col-md-5" for="s_3">Получено</label>';
                echo CheckboxX::widget([
                'name'=>'ViberTransaction[status][]',
                'options'=>['id'=>'s_3'],
                    'value'=>$messagePhoneList::DELIVERED,
                'pluginOptions'=>['threeState'=>false]
                ]); ?>
            </div>
            <div class="form-group">
                <?php echo '<label class="cbx-label col-md-5" for="s_4">Прочитано</label>';
                echo CheckboxX::widget([
                'name'=>'ViberTransaction[status][]',
                'options'=>['id'=>'s_4'],
                    'value'=>$messagePhoneList::VIEWED,
                'pluginOptions'=>['threeState'=>false]
                ]); ?>
            </div>
        </div>
        <div class="form-group col-md-12">
            <?= Html::submitButton("<i class='glyphicon glyphicon-search'></i>Поиск", ['class' => 'btn btn-success']) ?>
            <?= Html::submitButton("<i class='fa fa-file-excel-o'></i> Скачать отчет", ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
     </div>

    <div class="col-md-12">
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'created_at',
                'value'=> function($model){
                    return date('d:m:Y',$model->created_at);
                }
            ],
            [
                'attribute'=>'telephones',
                'header'=>'Телефоны',
                'format'=>'raw',
                'value'=>function($model)use($post){
                    return $model->Phone($model,$post);;
                }
            ],
            [
                'attribute'=>'viber_message_id',
                'value'=>function($model){
                    return $model->viberMessage->text;
                }
            ],
        ],
    ]);?>
    </div>
    <?php Pjax::end(); ?>
</div>
