<?php

use yii\helpers\Html;
use kartik\grid\GridView;
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
                'class' => 'kartik\grid\CheckboxColumn',
                'headerOptions' => ['class' => 'kartik-sheet-style'],
                // you may configure additional properties here
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'phone',
                'editableOptions'=> function ($model, $key, $index) {
                    return [
                        'header'=>Yii::t('front','edit_phohe'),
                        'size'=>'md',
                        'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                    ];
                }
            ],
            [
                'class'=>'kartik\grid\EditableColumn',
                'attribute'=>'username',
                'editableOptions'=> function ($model, $key, $index) {
                    return [
                        'header'=>Yii::t('front','edit_phohe'),
                        'size'=>'md',
                        'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
                    ];
                }
            ],


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>