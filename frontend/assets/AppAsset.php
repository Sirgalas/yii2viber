<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use Yii;
/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];


    public function init()
    {
        
        $this->_includeJs();
        // $this->_includeCss();
    }
    private function _includeJs()
    {
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        if ($controller == "statistics" && $action == "index") {
            $this->js[] = "js/statistic.js";
        }

    }
}
