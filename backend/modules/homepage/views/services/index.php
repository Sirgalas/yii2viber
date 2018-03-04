<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\homepage\search\ServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить текст', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Добавить картинки', ['create-back'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'param',
                'format'=>'raw'
            ],
            [
                'attribute'=> 'text',
                'width'=>'200px',
                'format'=>'raw',
                'value'=>function($model){
                    if($model->param=='service_background')
                        return Html::img($model->getImageUrl(),['width'=>200]);
                    return $model->text;
                }
            ],

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}{update-back}{delete}{view}',
                'buttons' => [
                    'update-back' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-picture"></span>',$url);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
