<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\entities\ViberMessage $model
 * @var array $contact_collections
 * @var array $assign_collections
 */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Viber Message',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Viber Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="viber-message-update">

    <?= $this->render('_form',
        compact('model','contact_collections','assign_collections')
    ) ?>

</div>
