<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->username ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                    ['label' => 'Login', 'url' => ['auth/login'], 'visible' => Yii::$app->user->isGuest],
                    [ 'label' => 'Настройки главной', 'icon' => 'share', 'url' => '#', 'items' => [
                        ['label' => 'Слайдер', 'url' => ['/homepage/slider']],
                        ['label' => 'Текст приветствия', 'url' => ['/homepage/text-welcome']],
                        ['label' => 'Предлагамые сервисы', 'url' => ['/homepage/services']],
                        ['label' => 'Цены', 'url' => ['/homepage/price']],
                        ],
                    ],
                    ['label' => 'Баланс Лог', 'url' => ['/balance-log']],
                    ['label' => 'Infobip Сценарии', 'url' => ['/scenario']],
                ],
            ]
        ) ?>

    </section>

</aside>
