<?php /**
* @var $config common\entities\Config
*/
if($confug) {
    for ($i = 1; $i <= count($config); $i++) {
        $service = 'service_services_' . $i;
        ?>
        <div class="grid_3">
            <div class="element">
                <h3><?= Yii::$app->config->get($service); ?></h3>
            </div>
        </div>
    <?php }
}else{ ?>
    <div class="grid_8">
        <div class="element">
            <h3><?= Yii::$app->config->get('service_text'); ?></h3>
        </div>
    </div>
<?php } ?>