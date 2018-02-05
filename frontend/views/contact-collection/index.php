<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\ContactCollectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contact Collections');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-collection-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a(Yii::t('app', 'Create Contact Collection'), ['create'], ['class' => 'btn btn-success'])?>
        <?=Html::a(Yii::t('app', 'Add Selected into New'), ['copy-new'], ['class' => 'btn btn-success'])?>
        <?=Html::a(Yii::t('app', 'Add Selected into Exists'), ['copy'], ['class' => 'btn btn-success'])?>
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
                                    'template' => ' {update} {delete} {list}',
                                    'buttons' => [
                                        'list' => function ($url,$model) {
                                            return Html::a(
                                                '<i class="fa fa-fw  fa-phone-square"></i>',
                                                $url);
                                        },

                                    ],
                                ],
                            ],
                        ]);?>
    <?php Pjax::end(); ?>
</div>
