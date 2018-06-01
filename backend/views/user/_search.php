<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ModeratorSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php /*
<div class="moderator-form-search">

	<section class="row">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php // echo $form->field($model, 'id') ?>

    <div class="col-md-3"><?= $form->field($model, 'name') ?></div>
    <div class="col-md-3"><?= $form->field($model, 'phone') ?></div>   
    <div class="col-md-3"><?php  echo $form->field($model, 'email') ?></div>

    <?php // echo $form->field($model, 'status') ?>

    <?php //echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>
    
    </section>

    <div class="form-group">
    <div class="row">
    <div class="col-md-12">
    	<section class="pull-right">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
       </section> 
     </div>  
     </div> 
    </div>

    <?php ActiveForm::end(); ?>

</div>
*/ ?>
<?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
<div class="dashSearch dashSearch5col">
                <div class="rw">
                    <div class="colM srch">
                        <!-- <input type="text" class="form-control" name="" placeholder="Search "> -->
                        <?php echo $form->field($model, 'id')->textInput()->input('text', ['placeholder' => 'Search By User ID'])->label(false); ?>
                        <?php echo $form->field($model, 'phone')->textInput()->input('text', ['placeholder' => 'Search By phone'])->label(false); ?>
                    </div>
                    <div class="colM">
                        <!-- <select class="form-control">
                            <option>Status</option>
                            <option>Activiated</option>
                            <option>Unactivatied</option>
                            <option>Banned</option>
                            <option>Verified</option>
                            <option>Requested</option>
                            <option>All</option>
                        </select> -->
                        <?php echo $form->field($model, 'status')->dropDownList(['' => 'Status', '10' => 'Activated', '1' => 'Inactivated' , '2' => 'Requested', '3' => 'Banned' , '4' => 'Blocked', '0' => 'Deleted'])->label(false); ?>
                    </div>  
                    <div class="colM">
                        <?php echo $form->field($model, 'user_type')->dropDownList(['0' => 'Type', '3' => 'Individual', '4' => 'Organization' , '5' => 'Organization Subuser'])->label(false); ?>
                    </div>  
                    <div class="colM">
                        <?php echo $form->field($model, 'all_user')->dropDownList(['0' => 'New Users', '1' => 'All Users'])->label(false); ?>
                        <!-- <select class="form-control" name="UserSearch[all_user]">
                            <option value="0">New Users</option>
                            <option value="1">All Users</option>
                        </select> -->
                    </div>          
                    <div class="colM">   
                        <?= Html::submitButton('Search', ['class' => 'btn btn-warning btn-sm']) ?>                       
                        <!-- <input type="submit" value="Search" class="btn btn-warning btn-sm" name=""> -->
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>