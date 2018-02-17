<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use kartik\checkbox\CheckboxX;
/* @var $this yii\web\View */
/* @var $searchModel common\entities\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php $form=ActiveForm::begin(); ?>
    <?= $form->field($model,'titleSearh')->textInput(['placeholder'=>'Выбирите телефон или название рассылки'])->label(false) ?>
    <div class="col-md-5 col-md-offset-1">
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
        <?= $form->field($model, 'contactCollection')->widget(Select2::classname(), [
            'data' => $contact_collections,
            'maintainOrder' => true,
            'options' => [
            'placeholder' => 'Выберите коллекции ...',
            ],
        ])->label(false); ?>
        <?php if(!Yii::$app->user->identity->isClient()){
            echo $form->field($model, 'user_id')->widget(Select2::classname(), [
                'data' => $clients,
                'maintainOrder' => true,
                'options' => [
                    'placeholder' => 'Выберите коллекции ...',
                ],
            ]);
        }
        ?>
    </div>
    <div class="col-md-5">
        <?php echo '<label class="cbx-label" for="s_1">Доствлено</label>';
        echo CheckboxX::widget([
        'name'=>'ViberTransaction[status]',
        'options'=>['id'=>'s_1'],
        'pluginOptions'=>['threeState'=>false]
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::begin(); /*?>

    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->id];
                },
                'headerOptions' => ['width' => '40'],
            ],
            [
                'attribute' => 'id',
                'headerOptions' => ['width' => '40'],
            ],

            //'user_id',
            [
                'attribute' => 'title',

            ],
            //'type',
            [
                'attribute' => 'created_at',
                'format' => 'date',
                'headerOptions' => ['width' => '120'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '90'],
                'template' => ' {update} {delete}{view}{list}',
                'buttons' => [
                    'list' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw  fa-phone-square"></i>',
                            $url);
                    },

                ],
            ],
        ],
    ]);*/?>
    <?php Pjax::end(); ?>
</div>
