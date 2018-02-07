<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $phoneSearchModel common\entities\PhoneSearch */
/* @var $phoneDataProvider yii\data\ActiveDataProvider */


?>
<div class="phone-index">


    <?php Pjax::begin(['id' => 'pjax-grid-view']); ?>


    <?= GridView::widget([
        'dataProvider' => $phoneDataProvider,
        'filterModel' => $phoneSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\CheckboxColumn',
                // you may configure additional properties here
            ],
            //'id',
            //'user_id',
            //'username',
            'phone',


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>