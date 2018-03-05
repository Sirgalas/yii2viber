<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\Config */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Редактировать картинку', ['update-back', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('На главную', ['index', ], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=>'param',
                'format'=>'raw'
            ],
            [
                'attribute'=> 'text',
                'format'=>'raw',
                'value'=>function($model){
                    if($model->param=='service_background')
                        return Html::img($model->getImageUrl(),['width'=>200]);
                    return $model->text;
                }
            ],

        ],
    ]) ?>

</div>
