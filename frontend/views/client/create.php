<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\entities\user\User */
/* @var $balance common\entities\Balance */
/* @var $dealers array */

$this->title = Yii::t('app', 'Создать клиента');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <div class="viber-message-form" style="margin-top: 20px">
        <div class="box box-solid box-default">
            <div class="box-header">
                <h3 class="box-title"> V<?=Html::encode($this->title)?></h3>
            </div><!-- /.box-header -->
            <div class="box-body">


                <?=$this->render('_form', [
                    'model' => $model,
                    'balance'=>$balance,
                    'dealers'=>$dealers
                ])?>

            </div>
        </div>
    </div>
</div>
