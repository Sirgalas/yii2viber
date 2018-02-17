<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\FilesPhoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Files Phones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-phone-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Files Phone', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'file',
            'month',
            'years',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
