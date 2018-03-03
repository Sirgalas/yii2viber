<?php

use yii\helpers\Html;
use common\widgets\Alert;

use frontend\modules\home\assets\HomeAsset;
HomeAsset::register($this);
$this->title=Yii::$app->name;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= Html::encode($this->title) ?></title>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name = "format-detection" content = "telephone=no" />
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <script>
        $(document).ready(function () {
            if ($('html').hasClass('desktop')) {
                new WOW().init();
            }
        });
    </script>
</head>
<body>
<?php $this->beginBody() ?>
<body class="index">
<?= Alert::widget() ?>
<?= $this->render('header.php') ?>
<?= $content ?>
<?= $this->render('footer.php') ?>
<script>
    var _emv = _emv || [];
    _emv['campaign'] = '21928eda50b8059782c50d0c';

    (function() {
        var em = document.createElement('script'); em.type = 'text/javascript'; em.async = true;
        em.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'leadback.ru/js/leadback.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(em, s);
    })();
</script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>