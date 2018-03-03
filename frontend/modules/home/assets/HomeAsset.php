<?php

namespace frontend\modules\home\assets;

use yii\web\AssetBundle;

class HomeAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/home/web/';

    public $css = [
        'css/grid.css',
        'css/owl.carousel.css',
        'css/camera.css',
        'css/style.css'

    ];
    public $js = [
        'js/jquery-migrate-1.2.1.js',
        'js/camera.js',
        'js/owl.carousel.js',
        'js/jquery.stellar.js',
        'js/jquery.cookie.js',
        'js/TMForm.js',
        'js/modal.js',
        'js/device.min.js',
        'js/tmstickup.js',
        'js/jquery.easing.1.3.js',
        'js/jquery.ui.totop.js',
        'js/jquery.mousewheel.min.js',
        'js/jquery.simplr.smoothscroll.min.js',
        'js/superfish.js',
        'js/jquery.mobilemenu.js',
        'js/script.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}