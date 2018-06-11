<?php
use kartik\grid\GridView;
use frontend\widgets\StatisticGraphMongo;
use kartik\export\ExportMenu;
/**
 * @var $searchModel common\entities\mongo\Message_Phone_List;
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model common\entities\mongo\Message_Phone_List;
 * @var $providerFromGetModel yii\data\ActiveDataProvider
 */

$gridColumns = [
    [
        'attribute'=>'created_at',
        'header'=>'Дата рассылки',
        'value'=> function($model){
            return date('d:m:Y',$model->viberTransaction->created_at);
        }
    ],
    [
        'attribute'=>'phone',
        'header'=>'Phone',
    ],
    [
        'attribute'=>'status',
        'header'=>'Статус',
        'value'=>function($model){
            return $model->statusMessage;
        }
    ],
    [
        'attribute'=>'date_delivered',
        'header'=>'Дата доставки',
        'value'=> function($model){
            return ($model->date_delivered)?date('d:m:Y',$model->date_delivered):'не доставлено';
        }
    ],
    [
        'attribute'=>'date_viewed',
        'header'=>'Дата просмотра',
        'value'=> function($model){
            return ($model->date_viewed)?date('d:m:Y',$model->date_viewed):'не просмотрено';
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
];?>


<?= StatisticGraphMongo::widget(['model'=>$providerFromGetModel->getModels()]) ?>
<?php
//$exeportExelDataProvider->pagination=false;
$export =ExportMenu::widget([
    'dataProvider' => $exeportExelDataProvider,
    'enableFormatter'=>false,
    'columns' => $gridColumns,
    'target' => ExportMenu::TARGET_BLANK,
    'fontAwesome' => true,
    'pjaxContainerId' => 'kv-pjax-container',
    'showColumnSelector'=>false,
    'dropdownOptions' => [
        'label'=>'Скачать отчет',
        'fontAwesome' => true,
        'class'=> "btn btn-primary",
        'itemsBefore' => [
            '<li class="dropdown-header">Получить все данные</li>',
        ],
    ],
    'exportConfig'=>[
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_PDF => false,
        ExportMenu::FORMAT_HTML=> false,
        //ExportMenu::FORMAT_CSV=>false,
        ExportMenu::FORMAT_EXCEL => [
            'label' => Yii::t('kvexport', 'Excel 95 +'),
            'icon' => 'file-excel-o',
            'iconOptions' => ['class' => 'text-success'],
            'linkOptions' => [],
            'options' => ['title' => Yii::t('kvexport', 'Microsoft Excel 95+ (xls)')],
            'alertMsg' => Yii::t('kvexport', 'The EXCEL 95+ (xls) export file will be generated for download.'),
            'mime' => 'application/vnd.ms-excel',
            'extension' => 'xls',
            'encoding'=>'utf-8',
            'writer' => ExportMenu::FORMAT_EXCEL
        ],
        ExportMenu::FORMAT_EXCEL_X => [
            'label' => Yii::t('kvexport', 'Excel 2007+'),
            'icon' => 'file-excel-o' ,
            'iconOptions' => ['class' => 'text-success'],
            'linkOptions' => [],
            'options' => ['title' => Yii::t('kvexport', 'Microsoft Excel 2007+ (xlsx)')],
            'alertMsg' => Yii::t('kvexport', 'The EXCEL 2007+ (xlsx) export file will be generated for download.'),
            'mime' => 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'extension' => 'xlsx',
            'writer' => ExportMenu::FORMAT_EXCEL_X
        ],
    ]
]); ?>
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
    'headerRowOptions' => ['class' => 'kartik-sheet-style'],
    'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        $gridColumns[0],
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
    'toolbar' => [
            $export
    ],
    /*'export' => [
        'label'=>'Скачать отчет',
        'fontAwesome' => true,
        'options'=>[
            'class'=> "btn btn-primary"
        ],
    ],
    'exportContainer' => ['class' => 'btn-primary'],
    //'persistResize' => false,
    'toggleDataOptions' => ['minCount' => 10],
    'exportConfig' => $defaultExportConfig,
    'itemLabelSingle' => 'book',
    'itemLabelPlural' => 'books'*/
]);?>
