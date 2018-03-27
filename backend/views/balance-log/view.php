<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\BalanceLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Balance Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-log-view">



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
        ],
    ]) ?>

</div>
