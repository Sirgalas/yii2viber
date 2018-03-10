<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\entities\Scenario */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Scenarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scenario-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'provider',
            'name',
            'from1',
            'channel1',
            'from2',
            'channel2',
            'from3',
            'channel3',
            'default:boolean',
            'provider_scenario_id',
            'created_at',
        ],
    ]) ?>

</div>
