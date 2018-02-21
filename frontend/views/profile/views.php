<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
/**
 * @var $entities common\entities\user\User
 */

$this->title = $entities->username;
$this->params['breadcrumbs'][] = $this->title ?>
<div class="profile-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

<?= DetailView::widget([
    'model' => $entities,
    'attributes' => [
        'username',
        [
            'attribute'=>'first_name',
            'label'=>'Имя',
        ],
        [
            'attribute'=>'surname',
            'label'=>'Отчество',
        ],
        [
            'attribute'=>'family',
            'label'=>'Фамилия'
        ],
        'email:email',
        [
            'attribute'=>'balance',
            'label'=>'Баланс'
        ],
        [
            'attribute'=> 'tel',
            'label'=>'Баланс'
        ],
        [
            'attribute'=>'time_work',
            'label'=>'Время работы'
        ],
    ],
]); ?>

<?= Html::a(Yii::t('app', 'Изменить профиль'), Url::to('update'), ['class' => 'btn btn-primary']); ?>

<?= Html::a(Yii::t('app', 'Изменить прароль'), Url::to('/user/recovery/reset'),  ['class' => 'btn btn-success']); ?>
