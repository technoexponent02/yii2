<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Searches */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="searches-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'results')->textInput() ?>

    <?= $form->field($model, 'baths')->textInput() ?>

    <?= $form->field($model, 'rooms')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'search')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
