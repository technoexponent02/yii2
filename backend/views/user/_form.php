<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ModeratorForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moderator-form">
    <?php $form = ActiveForm::begin([
                                    'options' =>[
                                        'class' => 'form-horizontal'],
                                ]);
    ?>
    <div class="box-body">
        <?php echo $form->field($model, 'email', [
                                        'template' => "{label}\n<div class='col-md-6'>{input}</div>\n{hint}\n<div class='col-md-4'>{error}</div>",
                                        'labelOptions' => [ 'class' => 'col-sm-2' ]
                                    ])->textInput(['maxlength' => true,'class' => 'form-control']);?>

        <?php echo $form->field($model, 'first_name', [
                                        'template' => "{label}\n<div class='col-md-6'>{input}</div>\n{hint}\n<div class='col-md-4'>{error}</div>",
                                        'labelOptions' => [ 'class' => 'col-sm-2' ]
                                    ])->textInput(['maxlength' => true,'class' => 'form-control']);?>

        <?php echo $form->field($model, 'last_name', [
                                        'template' => "{label}\n<div class='col-md-6'>{input}</div>\n{hint}\n<div class='col-md-4'>{error}</div>",
                                        'labelOptions' => [ 'class' => 'col-sm-2' ]
                                    ])->textInput(['maxlength' => true,'class' => 'form-control']);?>
									
        <?php
            if(!$model->isNewRecord)
            {
                echo $form->field($model, 'password', [
                                        'template' => "{label}\n<div class='col-md-6'>{input}</div>\n{hint}\n<div class='col-md-4'>{error}</div>",
                                        'labelOptions' => [ 'class' => 'col-sm-2' ]
                                    ])->passwordInput(['class' => 'form-control']);
            }
        ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>