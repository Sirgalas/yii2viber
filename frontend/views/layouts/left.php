<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>
                <?php
                if (!Yii::$app->user->isGuest){
                    if (Yii::$app->user->identity->isAdmin()){
                        echo 'Админ';
                    } elseif ( Yii::$app->user->identity->isDealer()){
                        echo 'Дилер';
                    }
                }
                ?>
                </p>
                <p><?= is_object(Yii::$app->user->identity)?Yii::$app->user->identity->username:'User not Auth'; ?></p>


            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <?php
        $menuItems=[
            ['label' => 'Меню приложения', 'options' => ['class' => 'header']],];
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['/user/registration/register']];
            $menuItems[] = ['label' => 'Login', 'url' => ['/user/security/login']];
        } else {
            $menuItems[] =  ['label'=>'Коллекции контактов' ,'url'=> Url::toRoute(['/contact-collection'])];
            $menuItems[] =  ['label'=>'Рассылки' ,'url'=> Url::toRoute(['/viber-message'])];

            if (Yii::$app->user->identity->isDealer() || Yii::$app->user->identity->isAdmin()){
                $menuItems[] =  ['label'=>'Клиенты' ,'url'=> Url::toRoute(['/client'])];
            }
            $menuItems[] =  ['label'=>'Отчеты' ,'url'=> Url::toRoute(['/reports'])];
            $menuItems[] =  ['label'=>'Статистика' ,'url'=> Url::toRoute(['/statistics'])];
            } ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options'   => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items'     => $menuItems
            ]
        ) ?>

    </section>

</aside>
