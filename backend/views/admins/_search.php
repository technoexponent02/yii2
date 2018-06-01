<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AdminsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?php // echo $form->field($model, 'organization_name') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'verification_code') ?>

    <?php // echo $form->field($model, 'otp_request') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'parent_id') ?>

    <?php // echo $form->field($model, 'user_type') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'usr_lat') ?>

    <?php // echo $form->field($model, 'usr_lng') ?>

    <?php // echo $form->field($model, 'sign_up_ip') ?>

    <?php // echo $form->field($model, 'login_ip') ?>

    <?php // echo $form->field($model, 'last_login') ?>

    <?php // echo $form->field($model, 'account_balance') ?>

    <?php // echo $form->field($model, 'user_image') ?>

    <?php // echo $form->field($model, 'country_id') ?>

    <?php // echo $form->field($model, 'preferred_locale') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'user_device') ?>

    <?php // echo $form->field($model, 'user_browser') ?>

    <?php // echo $form->field($model, 'is_badge') ?>

    <?php // echo $form->field($model, 'is_online') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'pending_verification') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
