<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'last_login')->textInput() ?>

    <?= $form->field($model, 'account_balance')->textInput() ?>

    <?= $form->field($model, 'user_device')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_browser')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_online')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
