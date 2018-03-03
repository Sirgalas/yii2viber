<?php
/**
 * @var $sliderItems common\entities\Config;
 * @var $sliderItem common\entities\Config;
 */
?>
<?php foreach ($sliderItems as $sliderItem ){ ?>
<div class="item" data-src="<?= $sliderItem->imageUrl ?>">
    <div class="camera_caption fadeIn">
        <?= $sliderItem ->param; ?>
    </div>
</div>
<?php } ?>
