<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\entities\ViberMessageSearch $searchModel
 */

$this->title = Yii::t('app', 'Viber Рассылки');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="viber-message-index">
    <div class="page-header">
        <h1><?=Html::encode($this->title)?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Viber Message',
]), ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        [
            'attribute' => 'username',
            'header' => 'Клиент',
            'value' => 'user.username',
        ],
        'title',
        [
            'attribute' => 'image',
            'value' => function ($model) {
                if ($model->image) {
                    return '<img src="' . $model->image . '" class="grid-view-message-picture">';
                }
                return '';
            },
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
        ],
//            'title_button',
//            'url_button:url',
        'type',
//            'alpha_name',
//            'date_start',
//            'date_finish',
//            'time_start',
//            'time_finish',

        [
            'attribute' => 'status',
            'value' => function ($model) {
                $color = 'bg-aqua';
                if ($model->status == 'pre') {
                    $color = 'label-warning';
                } elseif ($model->status == 'new') {
                    $color = 'label-primary';
                } elseif ($model->status == 'cancel') {
                    $color = 'label-danger';
                } elseif ($model->status == 'process') {
                    $color = 'bg-olive';
                } elseif ($model->status == 'ready') {
                    $color = 'label-success';
                }

                return "<small class=\"label center $color \">{$model->status}</small>";
            },
            'format' => 'raw',
        ],
        //'limit_messages',
        'cost',
        //'balance',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}',
            'buttons' => [
                'delete' => function ($url, $model) {
                    if ($model->isDeleteble()) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'data' => ['method' => 'post',],
                            'title' => Yii::t('app', 'Delete'),
                            'class' => '',
                        ]);
                    } else {
                        return '';
                    }
                },
            ],
        ],
    ];
    if (Yii::$app->user->identity->isDealer()) {
        unset($columns['username']);
    }

    Pjax::begin();
    echo GridView::widget([
                              'dataProvider' => $dataProvider,
                              'filterModel' => $searchModel,
                              'columns' => $columns,
                              'responsive' => true,
                              'hover' => true,
                              'condensed' => true,
                              'floatHeader' => true,

                              'panel' => [
                                  'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
                                  'type' => 'info',
                                  'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Новая рассылка',
                                                      ['update'], ['class' => 'btn btn-success']),
                                  'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Обновить', ['index'],
                                                     ['class' => 'btn btn-info']),
                                  'showFooter' => false,
                              ],
                          ]);
    Pjax::end(); ?>

</div>
