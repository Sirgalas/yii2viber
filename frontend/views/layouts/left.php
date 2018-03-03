<?php

use yii\helpers\Html;
use yii\helpers\Url;
$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
?>
<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/logo.png" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>
                    <?php
                    if (! Yii::$app->user->isGuest) {
                        if (Yii::$app->user->identity->isAdmin()) {
                            echo 'Админ';
                        } elseif (Yii::$app->user->identity->isDealer()) {
                            echo 'Дилер';
                        }
                    }
                    ?>
                </p>
                <p class="notAuth"><?=is_object(Yii::$app->user->identity) ? Yii::$app->user->identity->username : 'Гость,</br> зарегистрируйся';?></p>
            </div>
        </div>
        <?php
        $menuItems = [
            ['label' => 'Меню приложения', 'options' => ['class' => 'header']],
        ];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Регистрация', 'url' => ['/user/registration/register']];
            $menuItems[] = ['label' => 'Вход', 'url' => ['/user/security/login']];
        } else {
            $menuItems[] = ['label' => 'Тестовая рассылка', 'url' => Url::toRoute(['/'])];
            $menuItems[] = ['label' => 'Базы номеров', 'url' => Url::toRoute(['/contact-collection']),'options'=>['class'=>($controller=='contact-collection')?'active':'not-active']];
            $menuItems[] = ['label' => 'Создать рассылки', 'url' => Url::toRoute(['/viber-message']),'options'=>['class'=>($controller=='viber-message')?'active':'not-active']];

            if (Yii::$app->user->identity->isDealer() || Yii::$app->user->identity->isAdmin()) {
                $menuItems[] = ['label' => 'Все клиенты', 'url' => Url::toRoute(['/client']),'options'=>['class'=>($controller=='client')?'active':'not-active']];
                $menuItems[] = ['label' => 'Профиль', 'url' => Url::toRoute(['/profile/views']),'options'=>['class'=>($controller=='profile')?'active':'not-active']];

            }
            $menuItems[] = ['label' => 'Все отчеты', 'url' => Url::toRoute(['/report']),'options'=>['class'=>($controller=='report')?'active':'not-active']];
            $menuItems[] = ['label' => 'Вся статистика', 'url' => Url::toRoute(['/statistics']),'options'=>['class'=>($controller=='statistics')?'active':'not-active']];
            $menuItems[] = ['label' => 'Реклама', 'url' => Url::toRoute(['/site/advertising']),'options'=>['class'=>($controller=='site'&&$action=='advertising')?'active':'not-active']];
        }
        ?>
        <?=dmstr\widgets\Menu::widget([
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => $menuItems,
            ])?>
        <?php
        if (Yii::$app->user->isGuest || ! Yii::$app->user->identity->dealer_id) {
            $id = Yii::$app->params['defaultDealer'];
        } else {
            $id = Yii::$app->user->identity->dealer_id;
        }
            echo \frontend\widgets\DealerViews::widget(['id' => $id]);
        ?>
    </section>
</aside>
