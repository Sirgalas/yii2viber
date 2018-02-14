<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\entities\ViberMessageSearch $searchModel
 */

$this->title = Yii::t('app', 'Viber Messages');
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
            'value' => 'user.username',
        ],
        'title',
        'image:image',
//            'title_button',
//            'url_button:url',
            'type',
//            'alpha_name',
//            'date_start',
//            'date_finish',
//            'time_start',
//            'time_finish',
        'status',
            'limit_messages',
        'cost',
        //'balance',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}{delete}'

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
                                  'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'],
                                                      ['class' => 'btn btn-success']),
                                  'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'],
                                                     ['class' => 'btn btn-info']),
                                  'showFooter' => false,
                              ],
                          ]);
    Pjax::end(); ?>

</div>
