<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\VarDumper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app', 'Update Admin: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');


$permission_list = array();

foreach ($permissions as $key => $value) 
{
	$permission_list[$key] = $value->description;
}
//VarDumper::dump($permissions);
//VarDumper::dump($permission_list);
//$current_logged_user = Yii::$app->user->identity; 
//VarDumper::dump($current_logged_user);
//$current_user_permissions = Yii::$app->authManager->getPermissionsByUser($current_logged_user->id);
//VarDumper::dump($current_user_permissions);
?>

<?php /*
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
*/ ?>
<?php $form = ActiveForm::begin([
                                    'options' =>[
                                        'class' => 'customForm',
										'enctype' => 'multipart/form-data'],
										// 'fieldConfig' => [
										// 	'options' => [
										// 		'tag' => false,
										// 	],
										// ],
                                ]);
    							?>
	<h2 class="secTl">Admin Profile Edit</h2>
	<div class="whitebox">
		<div class="adminDashboardrowCont">
			<div class="adminDashboardrow">
				<div class="hide"></div>
				<div class="colm pull-right">
					<div class="form-group">
						<label class="text-right fullblock">User ID</label>
						<div class="infoTxt"><?php echo $model->id; ?></div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="text-right fullblock">First Name</label>
								<?= $form->field($model, 'first_name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="text-right fullblock">Last Name</label>
								<?= $form->field($model, 'last_name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Username</label>
						<?= $form->field($model, 'username', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Email</label>
						<?= $form->field($model, 'email', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Phone number</label>
						<?= $form->field($model, 'phone', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Password</label>
						<?= $form->field($model, 'password_val', [
										'template' => "{input}\n{error}",
                                    ])->passwordInput(['class' => 'form-control'])?>
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Acesss</label>
						<?php echo $form->field($model, 'user_type')->dropDownList([ '2' => 'Admin', '7' => 'Supervisor'])->label(false); ?>
					</div>
					
						<?php 
						// echo $form->field($model, 'permissions[]')->checkboxList($permission_list,
						// 			[
		                                
		    //                                 'item'=>function ($index, $label, $name, $checked, $value){
		    //                                 		// $checked = Yii::$app->user->can($value) ? "checked" : "";
		    //                                         //Your custom html code for each element
		    //                                         return '<div class="custRdolist">
						// 									<label>
						// 										<input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checked.'>
						// 										<span class="ico"></span>
						// 										'.$label.'
						// 									</label>
						// 								</div><br/>';
		    //                                     }
      //                        				]                           
      //                        		)->label(false); 
							if (!empty($permission_list))
							{
								foreach ($permission_list as $key => $value) {
									$edited_user = Yii::$app->authManager->getAssignment($key, $model->id);
									//VarDumper::dump($edited_user);
									$checked = ($edited_user!=null) ? "checked" : "";
									echo '<div class="custRdolist">
															<label>
																<input type="checkbox" name="AdminMyAccountForm[permissions]['.$key.']" value="'.$key.'" '.$checked.'>
																<span class="ico"></span>
																'.$value.'
															</label>
														</div><br/>';
								}
							}

                             		?>
						<!-- <div class="custRdolist">
							<label>
								<input type="checkbox" name="" value="" checked="">
								<span class="ico"></span>
								Approve Posts
							</label>
						</div><br/>
						<div class="custRdolist">
							<label>
								<input type="checkbox" name="" value="" checked="">
								<span class="ico"></span>
								Give Warnings
							</label>
						</div><br/>
						<div class="custRdolist">
							<label>
								<input type="checkbox" name="" value="" checked="">
								<span class="ico"></span>
								Ban Users
							</label>
						</div><br/>
						<div class="custRdolist">
							<label>
								<input type="checkbox" name="" value="">
								<span class="ico"></span>
								Access To Admin Management
							</label>
						</div> -->
					
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="form-group customBtnset align-left no-gap">
			<a href="<?php 
						$params = array_merge(['admins/view'], ['id' => $model->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-default btn-sm">Cancel</a>
			<input type="submit" value="Save" class="btn btn-primary btn-sm">
			<!-- <a href="javascript:void(0);" class="btn btn-danger btn-sm">Delete</a> -->
			<a href="<?php 
						$params = array_merge(['admins/delete'], ['id' => $model->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0">Delete</a>
		</div>
	</div>
<?php ActiveForm::end(); ?>
