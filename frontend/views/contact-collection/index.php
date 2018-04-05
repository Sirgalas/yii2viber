<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\entities\ContactCollectionSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = "База контактов";
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute' => 'title',

    ],

    'size',
    [
        'attribute' => 'created_at',
        'format' => 'date',
        'headerOptions' => ['width' => '120'],
    ],
];

$columns[]=    [
        'class' => 'yii\grid\ActionColumn',
        'headerOptions' => ['width' => '90'],
        'template' => ' {update} {delete}{view}{export}',
        'buttons' => [
            'export' => function ($url,$model) {
                return Html::a(
                    '<i class="fa fa-fw fa-download"></i>',
                    $url, ['target'=>'_blank', 'class'=>'download']);
            },

        ],
    ] ;
?>
<div class="contact-collection-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=Html::a("Создать базу", ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns
    ]);?>
    <?php Pjax::end(); ?>
</div>
    <script>
        function initPage() {
            $('a.download').click(function(e){
                e.preventDefault();
                window.open($(this).attr('href'));
            });
        }
    </script>
<?php
$js = '
  
       $(document).ready(function() {initPage();});
';
$this->registerJs($js);