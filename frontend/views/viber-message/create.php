<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\entities\ViberMessage $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Viber Message',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Viber Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="viber-message-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
