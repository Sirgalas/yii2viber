<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\entities\FilesPhohe */

$this->title = 'Create Files Phohe';
$this->params['breadcrumbs'][] = ['label' => 'Files Phohes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="files-phohe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
