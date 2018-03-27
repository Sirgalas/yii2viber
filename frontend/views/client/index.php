<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Клиенты');
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->session->has(\frontend\controllers\ClientController::ORIGINAL_USER_SESSION_KEY)){
    $template = '{update} {delete} {switch}';
} else {
    $template = '{update} {delete}';
}

//Yii::$app->timeZone = 'Europe/Kiev';
$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'id',
        'headerOptions' => ['width' => '40'],
    ],
    'email:email',
    'attribute'=>'username'
    ];
if (Yii::$app->user->identity->isAdmin()  ) {
    //$columns[]=['attribute' => 'created_at', 'value' => function ($model) {
    //    return Yii::$app->getFormatter()->asDatetime($model->created_at);
    //}, 'format'  => 'raw', 'label' => 'Дата. Регист.'];
    $columns[]=['attribute' => 'created_at', 'value' => function ($model) {
        return date('Y-m-d', $model->created_at).' '.date('H:i', $model->created_at);
    }, 'format'  => 'raw', 'label' => 'Дата. Регист.'];

    //$columns[]='created_at:dateFormat';
    }
    //'password_hash',
    //'auth_key',

$columns[]=    [
        'attribute' => 'confirmed_at',
        'label' => 'Одбр.',
        'headerOptions' => ['width' => '30'],
        'value' => function ($model) {
            if ($model->confirmed_at) {
                return 'Yes';
            } else {
                return 'No';
            };
        },
        'filter' => ['Yes', 'No'],
    ];
$columns[]=    [
        'attribute' => 'blocked_at',
        'label' => 'Блк.',
        'headerOptions' => ['width' => '30'],
        'value' => function ($model) {
            if ($model->blocked_at) {
                return 'Yes';
            } else {
                return 'No';
            };
        },
        'filter' => ['Yes', 'No'],
    ];

$columns[]=    [
        'attribute' => 'type',
        'label'=>'Статус',
        'filter' => ['dealer' => 'dealer', 'client' => 'client'],
        'value'=>function($model){
            return $model->theStatus;
        }
    ];

$columns[]=    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'cost',
        'label'=>'Цена ',

        'value'=>function($model){
            if (!$model->cost){
                return '0.00';
            } else {
                return $model->cost;
            }
        },

        'editableOptions'=> function ($model, $key, $index) {
            return [
                'header'=>Yii::t('front','Цену'),
                'placement'=>'auto',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'formOptions' => [
                    'action' => yii\helpers\Url::toRoute('client/' . $model->id . '/change-cost'),
                ]
            ];
        }
    ];
$columns[]=    [
        'class'=>'kartik\grid\EditableColumn',
        'attribute'=>'balance',
        'label'=>'Баланс SMS',
        'value'=>function($model){return number_format($model->balance); },

        'editableOptions'=> function ($model, $key, $index) {
            return [
                'header'=>Yii::t('front','Баланс'),
                'placement'=>'auto',
                'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                'formOptions' => [
                    'action' => yii\helpers\Url::toRoute('client/' . $model->id . '/change-balance'),
                ]
            ];
        }
    ];

$columns[]=    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update}{delete}{switch}',//{balance}{password}
        'buttons' => [
            'balance' => function ($url, $model) {
                return Html::a('<i class="fa fa-fw   fa-money"></i>', $url);
            },

            'switch' => function ($url, $model) {
                return Html::a('<i class="fa fa-fw  fa-user-secret"></i>', $url);
            },
        ],
    ];

?>
<div class="user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a(Yii::t('app', 'Добавить клиента'), ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'pjax'=>true,
                            'pjaxSettings'=>[
                                'neverTimeout'=>true,
                                'loadingCssClass'=>false,
                                'options'=>[
                                    'id' => 'pjax-grid-view',

                                ],
                            ],
                            'columns' =>  $columns

                        ]);?>
    <?php Pjax::end(); ?>
</div>
