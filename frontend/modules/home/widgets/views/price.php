<?php

/**
 * @vor $config common\entities\Config
 */

for($i=1;$i<=(count($config)/2);$i++){
    $priceHeader='price'.$i.'_header';
    $priceBody='price'.$i.'_body';
    ?>
    <div class="grid_4">
        <div class="pricing">
            <header>
                <?= Yii::$app->config->get($priceHeader) ?>
            </header>
            <ul class="list-unstyled pricing-list margin-b-50">
                <?= Yii::$app->config->get($priceBody) ?>
            </ul>
        </div>
    </div>
<?php } ?>