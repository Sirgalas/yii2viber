<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3" style="min-width: 330px;!important;">
        <div class="panel panel-default" style="min-width: 330px;!important;">
            <div class="panel-heading">
                <h3 class="panel-title"><?=Html::encode($this->title)?></h3>
            </div>
            <div class="panel-body" style="min-width: 330px;!important;">
                <?php $form = ActiveForm::begin([
                                                    'id' => 'registration-form',
                                                    'enableAjaxValidation' => false,
                                                    'enableClientValidation' => true,
                                                ]); ?>

                <?=$form->field($model, 'email')?>

                <?=$form->field($model, 'username')?>

                <?php if ($module->enableGeneratingPassword == false): ?>
                    <?=$form->field($model, 'password')->passwordInput()?>
                <?php endif ?>

                <?=$form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::class

                )->label(false)?>

                <?=Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block'])?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?=Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login'])?>
        </p>
    </div>
</div>
