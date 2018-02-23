<?php
use sirgalas\Morris;
?>
<div class="col-md-6">
<?php
if(!empty($dataArr)){
    echo Morris\Donut::widget([
        'resize' => true,
        'element' => 'donutChart',
        'data' => $dataArr,
        'donutColor' => $background,
        'formatter'  => "function (y) { return  y +'%' }"
    ]);
}
?>
</div>
<div class="col-md-6">
    <ul class="statusLi col-md-6">
        <?= $datali ?>
    </ul>
</div>
