<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\VarDumper;
use common\models\User;
use common\models\PropertyReports;

/* @var $this yii\web\View */
/* @var $model backend\models\ModeratorForm */

$this->title = 'Organisation User Profile Edit: ' . $organisationMyAccountForm->name;
// VarDumper::dump($organisationMyAccountForm);
// die;

$property_reports = PropertyReports::find()->where(['parent_id' => $organisationMyAccountForm->id])->all();
$sub_users = User::find()->where(['parent_id' => $organisationMyAccountForm->id ])->andWhere(['!=', 'status', User::STATUS_INCOMPLETE])->all();
// VarDumper::dump($sub_users);
// die;
?>
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
				<h2 class="secTl">Organisation User Profile Edit</h2>
				<div class="whitebox">
					<div class="adminDashboardrowCont">
						<div class="adminDashboardrow">
							<div class="colm">
								<div class="adminUserCont">
									<div class="adminUserImage">
										<img id ="sel_img" src="<?php 
									$user_image = Yii::$app->frontendUrlManager->baseUrl.'/frontend_assets/images/default-user.svg';
									if ($organisationMyAccountForm->user_image != null)
									{
										$user_image= Yii::$app->UrlManager->baseUrl.'/upload/user_image/'. $organisationMyAccountForm->user_image;
									}
									echo $user_image ?>" alt=""/>
									</div>
									<div class="picSet">
										<label>
											Set to Default
											<!-- <input type="file"/> -->
									<?= $form->field(
											$organisationMyAccountForm, 
											'user_image', 
											['template' => "{input}\n{error}"]
                            				)->fileInput([])->label(false)?>
										</label>
									</div>
									<div class="custRdolist">
										<label>
											<input type="checkbox" 
											name="OrganisationMyAccountForm[is_badge]"  <?php echo $organisationMyAccountForm->is_badge == 10 ? "checked" : ""; ?>>
										
                            				
											<span class="ico"></span>
											Badge
										</label>
									</div>
								</div>
							</div>
							<div class="colm">
								<div class="form-group">
									<label class="text-right fullblock">User ID</label>
									<div class="infoTxt"><?php echo $organisationMyAccountForm->id; ?></div>
								</div>
								
								<div class="form-group">
									<label class="text-right fullblock">Organisation Name</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
									<?= $form->field($organisationMyAccountForm, 'organization_name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Admin Name</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
									<?= $form->field($organisationMyAccountForm, 
												'name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>

								<div class="form-group">
									<label class="text-right fullblock">Email</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
									<?= $form->field($organisationMyAccountForm, 'email', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Phone number</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value="" style="width:400px;"> -->
									<?= $form->field($organisationMyAccountForm, 'phone', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Password <span>If you don't want to change, leave blank</span> </label>
									<!-- <input type="password" class="form-control" placeholder="Text Text" value="" style="width:400px;"> -->
									<?= $form->field($organisationMyAccountForm, 'password_val', [
										'template' => "{input}\n{error}",
                                    ])->passwordInput(['class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Status</label>
									<!-- <select class="form-control">
										<option>Banned</option>
										<option>Verified</option>
									</select> -->
									<?php //echo $form->field($organisationMyAccountForm, 'status')->dropDownList(['' => 'Status', '3' => 'Banned', '1' => 'Unactivated', '10' => 'Activated'])->label(false); ?>
									<div class="form-group field-organisationmyaccountform-status required has-success">

										<select id="organisationmyaccountform-status" class="form-control" name="OrganisationMyAccountForm[status]" aria-required="true" aria-invalid="false">
										<option value="">Status</option>
										<option value="3" <?php echo $organisationMyAccountForm->status == 3 && $organisationMyAccountForm->pending_verification == 0 ? "selected" : "" ?>>Banned</option>
										<option value="1"  <?php echo $organisationMyAccountForm->status == 10 && $organisationMyAccountForm->pending_verification == 2 ? "selected" : "" ?>>Inactivated</option>
										<option value="10" <?php echo $organisationMyAccountForm->status == 10 && $organisationMyAccountForm->pending_verification == 0 ? "selected" : "" ?>>Activated</option>
										</select>

										<div class="help-block"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Reason</label>
									<!-- <select class="form-control">
										<option>Violating The Terms Of Service </option>
										<option>Reached the maximum number of reports</option>
									</select> -->
									<?php echo $form->field($organisationMyAccountForm, 'reason')->dropDownList(['' => 'Reason', 'Violating The Terms Of Service' => 'Violating The Terms Of Service', 'Reached the maximum number of reports' => 'Reached the maximum number of reports'])->label(false); ?>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
					<!-- <div class="spacer"></div> -->
					<div class="form-group">
						<!-- <label class="text-right fullblock">Reports Received</label>
						<div class="postsTable statustbl reportDet-table table-responsive">      
							<table class="table tableRtl table-border">
								<tr>
									<th width="120">Post ID</th>
									<th width="120">Added By</th>
									<th width="120">Date</th>
									<th width="150">Reason</th>
									<th width="120">Delete</th>
								</tr>
								<tr>
									<td>
										<a href="javascript:void(0);" class="edt">2837#</a>
									</td>
									<td>Subuser ID</td>
									<td>3/4/2017<br/>2:39PM</td>
									<td>
										<select class="form-control newSelect" style="min-width:240px;">
											<option>Property Information Are Inaccurate</option>
											<option>Low Resolution Picures</option>
											<option>Contacting Information Are Not Valid</option>
										</select>
									</td>
									<td>
										<a href="" class="btn btn-danger btn-sm">Delete</a>
									</td>
								</tr>
							</table>
						</div> -->
						<?php 
                            if ($property_reports != null)
                            {

                                ?>
                                <label class="text-right fullblock">Reports Received</label>
                                <div class="postsTable statustbl reportDet-table table-responsive"> 
                                <table class="table tableRtl table-border">
                                    <tr>
                                        <th width="120">Post ID</th>
                                        <th width="120">Date</th>
                                        <th width="150">Reason</th>
										<th width="120">Delete</th>
                                    </tr>
                                <?php 
                                foreach ($property_reports as $report_key => $report) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" class="edt"><?php echo $report->post_id ?></a>
                                        </td>
                                        <td><?php echo Yii::$app->formatter->format($report->created_at,'datetime'); ?></td>
                                        <td><?php echo $report->report->translatateData->name ?></td>
                                        <td>
											<a href="<?php 
						$params = array_merge(['user/organisation-report-delete'], ['id' => $report->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-danger btn-sm" data-method="post" data-pjax="0">Delete</a>
										</td>
                                    </tr>
                                    <?php 
                                }
                                ?>
                                </table>
                                </div>
                                <?php 
                            }
                            ?>   
					</div>
					<div class="form-group">
						<label class="text-right fullblock">Subusers</label>
							<div class="postsTable statustbl reportDet-table table-responsive">      
							<table class="table tableRtl table-border" id="tblSubUser">
						<?php 
						if ($sub_users != null)
						{
							?>
							
								<tr>
									<th width="120">User ID</th>
									<th width="120">Expiration In</th>
									<th width="140">User Name</th>
									<th width="120">Status</th>
									<th width="160">Password</th>
									<th width="90">&nbsp;</th>
								</tr>
							<?php 
							foreach ($sub_users as $key => $sub_user) {
								?>
								<tr>
									<td><?php echo $sub_user->id; ?><input type="hidden" name="OrganisationMyAccountForm[sub_user_id][]" value="<?php echo $sub_user->id; ?>"></td>
									<td>
										<?php if($sub_user->expiration_date){ 
												$time_left = floor(($sub_user->expiration_date - time())/(3600*24));
												if($time_left > 0){
													echo $time_left.' '.getDbLanguageText('days');
												}
												else
													echo "Expired";
											?>											
										<?php } else { ?>
											NA
										<?php } ?>
									</td>
									<td><input type="text" name="OrganisationMyAccountForm[sub_user_name][]" class="form-control" placeholder="" 
										value="<?php echo $sub_user->name; ?>" style="width:140mx; text-align:center;"></td>
									<td>
										<select name="OrganisationMyAccountForm[sub_user_status][]" class="form-control newSelect" style="width:116px;">
											<option value="<?php echo User::STATUS_ACTIVE; ?>" <?php echo ($sub_user->status == User::STATUS_ACTIVE)? "selected" :""; ?>>Active</option>
											<option value="<?php echo User::STATUS_REQUESTED; ?>" <?php echo ($sub_user->status == User::STATUS_REQUESTED)? "selected" :""; ?>>Requested</option>
											<option value="<?php echo User::STATUS_BLOCKED; ?>" <?php echo ($sub_user->status == User::STATUS_BLOCKED)? "selected" :""; ?>>Blocked</option>
											<option value="<?php echo User::STATUS_DELETED; ?>" <?php echo ($sub_user->status == User::STATUS_DELETED)? "selected" :""; ?>>Deleted</option>
										</select>
									</td>
									<td>
										<input type="text" name="OrganisationMyAccountForm[sub_user_password][]" class="form-control" placeholder="" value="" style="width:140mx; text-align:center;">
									</td>
									<td>
										<?php if ($sub_user->status == User::STATUS_DELETED){
											?>
											<span class="btn btn-danger btn-xs">Deleted</span>
											<?php 
										}
										else 
										{
											?>
											<a href="<?php 
						$params = array_merge(['user/organisation-subuser-delete'], ['id' => $sub_user->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-danger btn-sm" data-method="post" data-pjax="0">Delete</a>
											<?php 
										} ?>
										
									</td>
								</tr>
								<?php 
							}
							?>
							
							<?php 
						} 
						?>
						
								
								<!-- <tr>
									<td>8273#</td>
									<td>First and Last Name</td>
									<td>
										<select class="form-control newSelect" style="width:116px;">
											<option>Active</option>
											<option>Requested</option>
											<option>Blocked</option>
										</select>
									</td>
									<td>
										<input type="text" class="form-control" placeholder="" value="1234" style="width:140mx; text-align:center;">
									</td>
									<td>
										<a href="" class="btn btn-danger btn-sm">Delete</a>
									</td>
								</tr> -->
						</table>
						</div>	
					</div>
					<div class="form-group customBtnset">
						<a href="javascript:void(0);" class="btn btn-primary btn-sm" id ="addSubUser">Add Subuser</a>
						<?php if($request_sub_user !=null) { ?>
						<a href="<?php 
						$params = array_merge(['user/organisation-subuser-review'], ['id' => $request_sub_user->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-success btn-sm" data-method="post" data-pjax="0">Request Reviewed</a>
						<?php } ?>
					</div>
					<div class="form-group customBtnset align-left no-gap">
						<a href="<?php 
						$params = array_merge(['user/view'], ['id' => $organisationMyAccountForm->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-default btn-sm">Cancel</a>
						<input type="submit" value="Save" class="btn btn-primary btn-sm">
						
						<a href="<?php 
						$params = array_merge(['user/organisation-delete'], ['id' => $organisationMyAccountForm->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0">Delete</a>
						
					</div>
				</div>
			<?php ActiveForm::end(); ?>
<?php 
$js = <<<JS
$(document).ready(function() {
		$('#organisationmyaccountform-user_image').change( function(event) {
            var tmppath = URL.createObjectURL(event.target.files[0]);
            $("#sel_img").fadeIn("fast").attr('src',tmppath);			
            });
        });
        $('#addSubUser').click( function(){
        	var sub_user_html = '<tr><td>#<input type="hidden" name="OrganisationMyAccountForm[sub_user_id][]" value="0"></td><td><input type="text" name="OrganisationMyAccountForm[sub_user_name][]" class="form-control" placeholder="" value="" style="width:140mx; text-align:center;"></td><td><select name="OrganisationMyAccountForm[sub_user_status][]" class="form-control newSelect" style="width:116px;"><option value="10">Active</option><option value="2">Requested</option><option value="4">Blocked</option></select></td><td><input type="text" name="OrganisationMyAccountForm[sub_user_password][]" class="form-control" placeholder="" value="123456" style="width:140mx; text-align:center;"></td></tr>';
        	$('#tblSubUser').append(sub_user_html);
        })
JS;
$this->registerJs($js);
?>