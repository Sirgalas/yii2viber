<?php
/**
 * @var $config common\entities\Config;
*/

use frontend\modules\home\widgets\SliderWidget;
use frontend\modules\home\widgets\ServicesWidget;
use frontend\modules\home\widgets\PriceWidget;
?>
<section id="content">
    <div class="full-width-container block-1">
        <div class="camera_container">
            <div id="camera_wrap">
                <?= SliderWidget::widget(); ?>
            </div>
        </div>
    </div>
    <div class="full-width-container block-2">
        <div class="container">
            <div class="row">
                <div class="grid_12">
                    <header>
                        <h2><span><?= (Yii::$app->config->get('welcome_text_header')!=null) ? Yii::$app->config->get('welcome_text_header'):""; ?></span></h2>
                    </header>
                    <?= (Yii::$app->config->get('welcome_text')!=null)?Yii::$app->config->get('welcome_text'):""; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="full-width-container block-3 parallax-block" style='background: url(<?= (Yii::$app->config->get('service_background')!=null)?$config->uploadUrl.'/'.Yii::$app->config->get('service_background'):''; ?>)' data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row">
                <div class="grid_12">
                    <header>
                        <h2><span> <?= (Yii::$app->config->get('service_header')!=null)?Yii::$app->config->get('service_header'):""; ?></span></h2>
                    </header>
                </div>
                <?= ServicesWidget::widget(); ?>
            </div>
        </div>

    </div>
    <div class="full-width-container block-4">
        <div class="container">
            <div class="row">
                <div class="grid_12">
                    <header>
                        <h2><span>Цены за сообщение</span></h2>
                    </header>
                </div>
            </div>
            <div class="row">
                <div id="owl-carousel" class="owl-carousel">
                    <?= PriceWidget::widget(); ?>
                </div>
            </div>
        </div>
    </div>
    <!--<div class="full-width-container block-5">
        <div class="container">
            <div class="row">
                <div class="grid_12">
                    <header>
                        <h2><span>News</span></h2>
                    </header>
                </div>
                <div class="grid_4">
                    <article>
                        <h3><a href="#">November 2014</a></h3>
                        <p>Gamus at magna non nunc tristique rhoncuseri tym. Aliquam nibh ante, egestas id dictum aterert commodo re luctus libero. Praesent faucibus malesuada cibuste.</p>
                        <a href="#" class="btn">More</a>
                    </article>
                </div>
                <div class="grid_4">
                    <article>
                        <h3><a href="#">March 2015</a></h3>
                        <p>Damus at magna non nunc tristique rhoncuseri tym. Aliquam nibh ante, egestas id dictum aterert commodo re luctus libero. Praesent faucibus malesuada cibust.</p>
                        <a href="#" class="btn">More</a>
                    </article>
                </div>
                <div class="grid_4">
                    <article>
                        <h3><a href="#">June 2015</a></h3>
                        <p>Jamus at magna non nunc tristique rhoncuseri tym. Aliquam nibh ante, egestas id dictum aterert commodo re luctus libero. Praesent faucibus malesuadaonec. </p>
                        <a href="#" class="btn">More</a>
                    </article>
                </div>
            </div>
        </div>
    </div>-->
</section>
