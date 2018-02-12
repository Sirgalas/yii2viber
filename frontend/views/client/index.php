<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?=GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'pjax'=>true,
                            'pjaxSettings'=>[
                                'neverTimeout'=>true,
                                'loadingCssClass'=>false,
                                'options'=>[
                                    'id' => 'pjax-grid-view',

                                ],
                            ],
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                    'attribute' => 'id',
                                    'headerOptions' => ['width' => '40'],
                                ],
                                'username',
                                'email:email',
                                //'password_hash',
                                //'auth_key',
                                [
                                    'attribute' => 'confirmed_at',
                                    'label' => 'Confirmed',
                                    'headerOptions' => ['width' => '40'],
                                    'value' => function ($model)  {
                                        if ($model->confirmed_at) {
                                            return 'Yes';
                                        } else {
                                            return 'No';
                                        };
                                    },
                                    'filter' => ['Yes', 'No'],
                                ],
                                [
                                    'attribute' => 'blocked_at',
                                    'label' => 'Blocked',
                                    'headerOptions' => ['width' => '40'],
                                    'value' => function ($model)  {
                                        if ($model->blocked_at) {
                                            return 'Yes';
                                        } else {
                                            return 'No';
                                        };
                                    },
                                    'filter' => ['Yes', 'No'],
                                ],
                                //'unconfirmed_email:email',

                                //'registration_ip',
                                //'created_at',
                                //'updated_at',
                                //'flags',
                                //'last_login_at',
                                [
                                    'attribute' => 'type',
                                    'filter' => ['dealer' => 'dealer', 'client' => 'client'],
                                ],
                                //'dealer_id',

                                [
                                    'class'=>'kartik\grid\EditableColumn',
                                    'attribute'=>'balance',
                                    'format'=>'currency',
                                     'editableOptions'=> function ($model, $key, $index) {
                                        return [
                                            'header'=>Yii::t('front','edit_balance'),
                                            'size'=>'md',
                                            'inputType'=>\kartik\editable\Editable::INPUT_MONEY,
                                            'formOptions' => [
                                                'action' => yii\helpers\Url::toRoute('client/' . $model->id . '/change-balance'),
                                            ]
                                        ];
                                    }
                                ],
                                'dealer_confirmed:boolean',
                                //'image',

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update}{balance}{password}{delete}',
                                    'buttons' => [
                                        'balance' => function ($url, $model) {
                                            return Html::a('<i class="fa fa-fw   fa-money"></i>', $url);
                                        },
                                        'password' => function ($url, $model) {
                                            return Html::a('<i class="fa fa-fw  fa-user-secret"></i>', $url);
                                        },
                                    ],
                                ],
                            ],
                        ]);?>
    <?php Pjax::end(); ?>
</div>
