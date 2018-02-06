<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $modelCollections common\entities\ContactCollection */

$this->title = $modelCollections->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contact Collections'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-collection-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $modelCollections->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $modelCollections->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $modelCollections,
        'attributes' => [
            'id',
            'user_id',
            'title',
            'type',
            'created_at',
        ],
    ]) ?>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'phone',
            'username',
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['width' => '90'],
                'template' => ' {create} {update}{view}{delete}',
                'buttons' => [
                    'create' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw  fa-phone-square"></i>',
                            Url::to(['/phone/create','id'=>$model->_id]));
                    },
                    'update' =>function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw  fa-phone-square"></i>',
                            Url::to(['/phone/update','id'=>$model->_id]));
                    },
                    'views' =>function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw  fa-phone-square"></i>',
                            Url::to(['/phone/views','id'=>$model->_id]));
                    },
                    'delete' =>function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-fw  fa-phone-square"></i>',
                            Url::to(['/phone/delete','id'=>$model->_id]));
                    },

                ],
            ],
        ],
    ]);?>
</div>
