<?php
use kartik\grid\GridView;
use yii\helpers\Html;
/**
* @var $searchModel common\entities\ViberTransaction
* @var $dataProvider yii\data\ActiveDataProvider
* @var $model common\entities\ViberTransaction
*/
$defaultExportConfig = [
    GridView::EXCEL => [
        'label' => Yii::t('kvgrid', 'Excel'),
        'icon' =>'file-excel-o',
        'iconOptions' => ['class' => 'text-success'],
        'showHeader' => true,
        'showPageSummary' => true,
        'showFooter' => true,
        'showCaption' => true,
        'filename' => Yii::t('kvgrid', 'grid-export'),
        'alertMsg' => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
        'options' => ['title' => Yii::t('kvgrid', 'Microsoft Excel 95+')],
        'mime' => 'application/vnd.ms-excel',
        'config' => [
            'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
            'cssFile' => ''
        ]
    ],
]

?>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute'=>'created_at',
            'value'=> function($model){
                return date('d:m:Y',$model->created_at);
            }
        ],
        [
            'attribute'=>'telephones',
            'header'=>'Телефоны',
            'format'=>'raw',
            'value'=>function($model){
                return $model->Phone();
            }
        ],
        [
            'attribute'=>'viber_message_id',
            'header'=>'Текст',
            'value'=>function($model){
                return $model->viberMessage->text;
            }
        ],
    ],
    'bordered'=>false,
    'striped'=>true,
    'condensed'=>true,
    'responsive'=>true,
    'hover'=>true,
    'showPageSummary' => false,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => false,
    ],
    'toolbar' =>  [
        '{export}',
    ],
    'export' => [
        'label'=>'Скачать отчет',
        'fontAwesome' => true,
        'options'=>[
            'class'=> "btn btn-primary"
        ],
    ],
    'exportContainer' => ['class' => 'btn-primary'],
    'persistResize' => false,
    'toggleDataOptions' => ['minCount' => 10],
    'exportConfig' => $defaultExportConfig,
    'itemLabelSingle' => 'book',
    'itemLabelPlural' => 'books'
]);?>
