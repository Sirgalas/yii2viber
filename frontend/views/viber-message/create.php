<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\entities\ViberMessage $model
 * @var array $contact_collections
 * @var array $assign_collections
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Viber Message',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Viber Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="viber-message-create">

    <?= $this->render('_form',  compact('model','contact_collections','assign_collections','clients')) ?>

</div>
