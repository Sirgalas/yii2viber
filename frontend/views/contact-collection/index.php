<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\ContactCollectionSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = "База контактов";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-collection-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=Html::a("Создать базу", ['create'], ['class' => 'btn btn-success'])?>
    </p>
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
            /*[
                'attribute' => 'id',
                'headerOptions' => ['width' => '40'],
            ],*/

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
                'template' => ' {update} {delete}{view}{export}',
                'buttons' => [
                    'export' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw fa-download"></i>',
                            $url, ['target'=>'_blank']);
                    },

                ],
            ],
        ],
    ]);?>
    <?php Pjax::end(); ?>
</div>
