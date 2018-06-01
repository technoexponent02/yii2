<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
?>
<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
	<div class="adminLoginArea">
		<div class="panel panel-warning panelcs loginBox">
		  <div class="panel-heading"><h2 class="patitle text-right">Admin Login</h2></div>
		  <div class="panel-body">

		    <div class="form-group mrgap">
		    	<label class="text-right fullblock">Username</label>
                <!-- <input type="text" class="form-control" name=""> -->
                <?= $form->field($model, 'username')->textInput(['autofocus' => true,'class' =>'form-control'])->label(false); ?>
		    </div>
		    

		    <div class="form-group mrgap">
		    	<label class="text-right fullblock">Passward</label>
                <!-- <input type="password" class="form-control" name=""> -->
                <?= $form->field($model, 'password')->passwordInput(['class' =>'form-control'])->label(false); ?>
		    </div>

		    <div class="clearfix">
		    	<!-- <input type="button" class="btn submitbtn pull-left" value="Login" name=""> -->
                <?= Html::submitButton('Login', ['class' => 'btn submitbtn pull-left', 'name' => 'login-button']) ?>
                <label class="remebrtxt pull-right">
		    		Remember Me
                    <!-- <input type="checkbox" class="hidden" name=""> -->
                    <?= $form->field($model, 'rememberMe')->checkbox([ 'class' => 'hidden'])->label(false); ?>
		    		<span></span>
		    	</label>
		    </div>


		    <div class="bordertop mrgtop50"></div>
		    <!-- <div class="linktext text-center">
		        <a href="javascript:void(0);" class="forgotClk">?Forgot</a>
		    </div> -->
		  </div>
		</div>
		
		<!-- <div class="panel panel-warning panelcs forgotpassBox">
		  <div class="panel-heading"><h2 class="patitle text-right">Reset Login Details</h2></div>
			<div class="panel-body">
				<div class="forgotPassFlds">
					<div class="form-group mrgap">
						<label class="text-right fullblock">Email</label>
						<input type="text" class="form-control" name="">
					</div>
					<div class="clearfix">
						<input type="button" class="btn submitbtn pull-left reqSuccess" value="Request  " name="">
					</div>
				</div>
				<div class="forgotPassMsg" dir="ltr" style="display:none;">
					Your login details has been sent to your email.
				</div>
			</div>
		</div> -->
    </div>
<?php ActiveForm::end(); ?>
<?php /* ?>
<div class="login-box">
    <div class="login-logo">
        <span><b>Admin</b></span>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true,'class' =>'form-control']) ?>
        </div>
        <div class="form-group has-feedback">
            <?= $form->field($model, 'password')->passwordInput(['class' =>'form-control']) ?>
        </div>
        <div class="row">
             <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </label>
              </div>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
  
</div>
<?php */ ?>
<?php 
//$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.GOOGLE_MAPS_API_KEY.'&libraries=places');
$js = <<<JS
$(document).ready(function(){
		$("body").on("click",".forgotClk", function(){
			$(".loginBox").css({"display":"none"});
			$(".forgotpassBox").css({"display":"block"});
		});
		$("body").on("click",".reqSuccess", function(){
			$(".forgotPassFlds").css({"display":"none"});
			$(".forgotPassMsg").css({"display":"block"});
		});
    });
JS;
$this->registerJs($js);
?>