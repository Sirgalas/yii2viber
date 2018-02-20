<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\ContactCollection */

$this->title = Yii::t('app', 'Создать базу');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'База телефонов'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-collection-create">



    <?= $this->render('_form',  compact('model', 'phoneSearchModel', 'phoneDataProvider')) ?>

</div>
