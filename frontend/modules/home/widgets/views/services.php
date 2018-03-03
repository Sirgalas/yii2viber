<?php /**
* @vor $config common\entities\Config
*/

for($i=1;$i<=count($config);$i++){
    $service='service_services_'.$i;
?>
<div class="grid_3">
    <div class="element">
        <h3><?= Yii::$app->config->get($service); ?></h3>
    </div>
</div>
<?php } ?>