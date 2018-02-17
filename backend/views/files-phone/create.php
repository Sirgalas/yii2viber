<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\FilesPhone */

$this->title = 'Create Files Phone';
$this->params['breadcrumbs'][] = ['label' => 'Files Phones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-phone-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
