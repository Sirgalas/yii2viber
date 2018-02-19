<?php
use kartik\grid\GridView;
/**
 * @var $searchModel common\entities\mongo\Message_Phone_List;
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model common\entities\mongo\Message_Phone_List;
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
            'header'=>'Дата рассылки',
            'value'=> function($model){
                
                return date('d:m:Y',$model->viberTransaction->created_at);
            }
        ],
        [
            'attribute'=>'phone',
            'header'=>'Телефон',
        ],
        [
            'attribute'=>'status',
            'header'=>'Статус',
            'value'=>function($model){
                return $model::$statusMessage[$model->status];
            }
        ],
        [
            'attribute'=>'date_delivered',
            'header'=>'Дата доставки',
            'value'=> function($model){
                return ($model->date_delivered)?'доставлено '.date('d:m:Y',$model->date_delivered):'не доставлено';
            }
        ],
        [
            'attribute'=>'date_viewed',
            'header'=>'Дата просмотра',
            'value'=> function($model){
                return ($model->date_viewed)?'просмотрено '.date('d:m:Y',$model->date_viewed):'не просмотрено';
            }
        ],
        [
            'attribute'=>'viber_message_id',
            'header'=>'Название рассылки',
            'format'=>'raw',
            'value'=>function($model){
                return \yii\helpers\Html::a($model->viberMessage->title,\yii\helpers\Url::to(['/viber-message/update','id'=>$model->viberMessage->id]));
            }
        ],
    ],
    'bordered'=>false,
    'striped'=>false,
    'condensed'=>false,
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
