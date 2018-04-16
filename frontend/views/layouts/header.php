<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\widgets\WantDealer;
/* @var $this \yii\web\View */
/* @var $content string */

if (Yii::$app->user->isGuest){
    Yii::$app->name = 'Viber рассылки';

}
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <?php  $x=Yii::info('test message');

            ?>
            <ul class="nav navbar-nav">
                
                <?php if(!Yii::$app->user->isGuest&&(Yii::$app->user->identity->isAdmin()||Yii::$app->user->identity->isDealer())){ ?>
                    <li>
                        ссылка для регистариции ваших клиентов</br> <?= Yii::$app->params['frontendHostInfo'].'/?id='.Yii::$app->user->identity->token; ?>
                    </li>
                    <li>
                        ваш токен <?=Yii::$app->user->identity->token; ?>
                    </li>
                <?php } ?>
                
                    <?= WantDealer::widget(); ?>


                <li>
                    <?php
                    if (Yii::$app->session->has(\frontend\controllers\ClientController::ORIGINAL_USER_SESSION_KEY)){
                        echo Html::a('Вернуться в свой аккаунт', '/client/switch',['class'=>'btn btn-block btn-info btn-lg']);
                    }
                    ?>
                </li>
                <li class="dropdown tasks-menu">
                    <?php if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isClient()){ ?>
                       <?= Html::a('Хочу стать дилером', Url::to(['/client/want-dealer'])); ?>
                    <?php } ?>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="overflow: hidden">
                        <!--<img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/> -->
                        <span class="hidden-xs"><?= is_object(Yii::$app->user->identity)?'Баланс'. Yii::$app->user->identity->headerInfo() :'Вы не авторизованы'; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">

                            <p>
                                <?php if (is_object(Yii::$app->user->identity)):  ?>
                                <?= Yii::$app->user->identity->username; ?>111
                                <small>Member since Nov. <?= date('Y-m-d',Yii::$app->user->identity->created_at) ?></small>
                                <?php if(!Yii::$app->user->isGuest&&(Yii::$app->user->identity->isAdmin()||Yii::$app->user->identity->isDealer())){ ?>
                                    <p>
                                        ссылка для регистрации ваших клиентов <?= Yii::$app->params['frontendHostInfo'].'/?id='.Yii::$app->user->identity->token; ?>
                                    </p>
                                    <p>
                                        Ваш токен <?= Yii::$app->user->identity->token; ?>
                                    </p>
                                    <?php } ?>
                                <?php else: ?>
                                Не авторизован
                                <?php endif ?>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <?php if(!Yii::$app->user->isGuest){ ?>
                        <?php } ?>
                        <!-- Menu Footer-->
                        <?php if (is_object(Yii::$app->user->identity)):  ?>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= Url::to(['/profile/views']) ?>" class="btn btn-default btn-flat">Профиль</a>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Выйти',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                        <?php endif ?>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>
