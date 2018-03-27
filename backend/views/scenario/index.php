<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\ScenarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сценарии для Infobip';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-index">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'provider',
            'name',
            'from1',
            'channel1',
            //'from2',
            //'channel2',
            //'from3',
            //'channel3',
            //'default:boolean',
            'provider_scenario_id',
            'created_at',

            ['class' => 'yii\grid\ActionColumn',
            'template'=>'{view}'
            ],
        ],
    ]); ?>
</div>
