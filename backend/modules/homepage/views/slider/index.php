<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\search\SliderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/**
 * @var $model common\entities\Config;
 */

$this->title = 'Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Создать слайдер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'param',
                'format'=>'raw',
                'label'=>'Текст'
            ],
            [
                'attribute'=>'text',
                'label'=>'Картинка',
                'format'=>'raw',
                'value'=>function($model){
                    return Html::img($model->imageUrl,['width'=>200]);
                }
            ],
            
            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
