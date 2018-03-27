<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\BalanceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Balance Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-log-index">

    <h1>Изменения баланса пользователей</h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'old_balance',
            'new_balance',
            'diff_balance',
            'controller_id',
            'action_id',
            'type',
            'fixed',
            'query',
            'post',
            'created_at',

            ['class' => 'yii\grid\ActionColumn',
              'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
