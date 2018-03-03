<header id="header">
    <div id="stuck_container">
        <div class="container">
            <div class="row">
                <div class="grid_12">
                    <h1><a href="index.html"><?= Yii::$app->name; ?></a><span><?= Yii::$app->params['nameHome'] ?></span></h1>
                    <nav>
                        <ul class="sf-menu">
                            <li class="current"><a href="<?= Yii::$app->homeUrl; ?>">Главная</a></li>
                            <?php  if (Yii::$app->user->isGuest) {  ?>
                            <li><a href="/user/registration/register">Регистрация</a> </li>
                            <li><a href="/user/security/login">Вход</a></li>
                            <?php } ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>