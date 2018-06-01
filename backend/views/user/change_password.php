<?php 

 ?>

<h2 class="secTl">Super Admin Password Change</h2>
	<div class="whitebox">
		<div class="form-group">
		<form id="change_password" name="change_password" method="post">
				<div class="form-group">
						<label class="text-right fullblock">New Password</label>
						<input class="form-control" type="password" name="password" minlength="8" required>
					</div>

					<div class="form-group">
						<label class="text-right fullblock">Confirm Password</label>
						<input class="form-control" type="password" name="confirmPassword" minlength="8" required>
						<p dir="ltr" class="text-right fullblock" id="msg_block"></p>
					</div>
						<input class="btn btn-warning btn-sm" type="submit" name="Save Changes">
					
		</form>
		</div>
	</div>

<?php 
$change_password =  Yii::$app->urlManager->createUrl(['user/change-password-process']); 
$js = <<<JS
$('#change_password').on('submit',function(e){
	e.preventDefault();
	form_data = $('#change_password').serialize();
	$.ajax({
		type: 'POST',
		url: '${change_password}',
		data : form_data, 
		success: function(result){
        if(result == 1){
        	$('#msg_block').html('Password changed successfully');
        	$('#change_password')[0].reset();
        }
        if(result == 2){
        	$('#msg_block').html('Passwords don\'t match!!');
        }
        if(result == 3){
        	$('#msg_block').html('Incorrect format');
        }
    }});

});
JS;
$this->registerJs($js);
?>
