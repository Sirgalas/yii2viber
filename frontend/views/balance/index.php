<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\search\BalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Balances';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Balance', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'viber',
                'editableOptions'=> function ($model, $key, $index) {
                    return [
                        'header'=>Yii::t('front','Viber'),
                        'placement'=>'auto',
                        'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                        'formOptions' => [
                            'action' => yii\helpers\Url::toRoute('balance/'.$model->id.'/edit-coast'),
                        ]
                    ];
                }
            ],
            'watsapp',
            'telegram',
            'wechat',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
