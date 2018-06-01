<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Searches */

$this->title = Yii::t('app', 'Create Searches');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Searches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="searches-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
