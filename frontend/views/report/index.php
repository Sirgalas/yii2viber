<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\entities\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php $form=ActiveForm::begin(); ?>

    <?php Pjax::begin(); ?>

    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'viber_message_id',
                'value'=>function($model){
                    return $model->viberMessage->title;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'     => $viberMessage,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Выбирите рассылку']
            ],
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return $model->theStatus;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter'     => $status,
                'filterWidgetOptions'=>[
                    'pluginOptions'=>['allowClear'=>true],
                ],
                'filterInputOptions'=>['placeholder'=>'Выбирите рассылку']
            ],
            [
                'attribute'=>'created_at',
                'value'=>function($model){
                    return date('d:m:Y',$model->created_at);
                },
            ],
            [
                'attribute'=>'collection_id',
                'format'=>'raw',
                'value'=>function($model){
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
                'filterInputOptions'=>['placeholder'=>'Выбирите базу телефонов']
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
