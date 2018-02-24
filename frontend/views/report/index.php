<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\entities\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Отчеты";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <h1><?=Html::encode($this->title)?></h1>

    <?php Pjax::begin(); ?>

    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'viber_message_id',
                'header'=>'Название рассылки',
                'value'=>function($model){
                    return $model->viberMessage->title;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'     => $viberMessage,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Выберите рассылку'],
                'group'=>true,
                'subGroupOf'=>2
            ],
            [
                'attribute'=>'status',
                'header'=>'Статус',
                'value'=>function($model){
                    return $model->theStatus;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'     => $status,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Выберите рассылку']
            ],
            [
                'attribute'=>'created_at',
                'value'=>function($model){
                    return date('d:m:Y',$model->created_at);
                },
                'group'=>true,
                'subGroupOf'=>1 
            ],
            [
                'attribute'=>'collection_id',
                'header'=>'База телефонов',
                'format'=>'raw',
                'value'=>function($model){
                    $arrCollection=[];
                    foreach ($model->viberMessage->contactCollection as $contactCollection){
                        $arrCollection[]=$contactCollection->title;
                    }
                    return implode(',</br>', $arrCollection);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'     => $contact_collections,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Выберите базу телефонов'],
                'group'=>true
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '90'],
                'template' => ' {list}',
                'buttons' => [
                    'list' => function ($url,$model) {
                        return Html::a(
                            'Сформировать отчет',
                            $url,['class'=>'btn btn-primary']);
                    },

                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
