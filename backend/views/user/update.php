<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ModeratorForm */
use common\models\PropertyReports;
$property_reports = PropertyReports::find()->where(['user_id' => $individualMyAccountForm->id])->all();

$this->title = 'Individual User Profile Edit: ' . $individualMyAccountForm->name;
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
				<h2 class="secTl">Individual User Profile Edit</h2>
				<div class="whitebox">
					<div class="adminDashboardrowCont">
						<div class="adminDashboardrow">
							<div class="colm">
								<div class="adminUserCont">
									<div class="adminUserImage">
										<img id ="sel_img" src="<?php 
									$user_image = Yii::$app->frontendUrlManager->baseUrl.'/frontend_assets/images/default-user.svg';
									if ($individualMyAccountForm->user_image != null)
									{
										$user_image= Yii::$app->UrlManager->baseUrl.'/upload/user_image/'. $individualMyAccountForm->user_image;
									}
									echo $user_image ?>" alt=""/>
									</div>
									<div class="picSet">
										<label>
											Set to Default
											<!-- <input type="file"/> -->
									<?= $form->field(
											$individualMyAccountForm, 
											'user_image', 
											['template' => "{input}\n{error}"]
                            				)->fileInput([])->label(false)?>
										</label>
									</div>
									<div class="custRdolist">
										<label>
											<input type="checkbox" 
											name="IndividualMyAccountForm[is_badge]"  <?php echo $individualMyAccountForm->is_badge == 10 ? "checked" : ""; ?>>
										
                            				
											<span class="ico"></span>
											Badge
										</label>
									</div>
								</div>
							</div>
							<div class="colm">
								<div class="form-group">
									<label class="text-right fullblock">User ID</label>
									<div class="infoTxt"><?php echo $individualMyAccountForm->id; ?></div>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="text-right fullblock">First Name</label>
											<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
											<?= $form->field($individualMyAccountForm, 'first_name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="text-right fullblock">Last Name</label>
											<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
											<?= $form->field($individualMyAccountForm, 'last_name', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Email</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value=""> -->
									<?= $form->field($individualMyAccountForm, 'email', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Phone number</label>
									<!-- <input type="text" class="form-control" placeholder="Text Text" value="" style="width:400px;"> -->
									<?= $form->field($individualMyAccountForm, 'phone', [
										'template' => "{input}\n{error}",
                                    ])->textInput(['maxlength' => true,'class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Password <span>If you don't want to change, leave blank</span> </label>
									<!-- <input type="password" class="form-control" placeholder="Text Text" value="" style="width:400px;"> -->
									<?= $form->field($individualMyAccountForm, 'password_val', [
										'template' => "{input}\n{error}",
                                    ])->passwordInput(['class' => 'form-control'])?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Status</label>
									<!-- <select class="form-control">
										<option>Banned</option>
										<option>Verified</option>
									</select> -->
									<?php echo $form->field($individualMyAccountForm, 'status')->dropDownList(['' => 'Status', '10' => 'Verified', '3' => 'Banned'])->label(false); ?>
								</div>
								<div class="form-group">
									<label class="text-right fullblock">Reason</label>
									<!-- <select class="form-control">
										<option>Violating The Terms Of Service </option>
										<option>Reached the maximum number of reports</option>
									</select> -->
									<?php echo $form->field($individualMyAccountForm, 'reason')->dropDownList(['' => 'Reason', 'Violating The Terms Of Service' => 'Violating The Terms Of Service', 'Reached the maximum number of reports' => 'Reached the maximum number of reports'])->label(false); ?>
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
									<th width="120">Date</th>
									<th width="150">Reason</th>
									<th width="120">Delete</th>
								</tr>
								<tr>
									<td>
										<a href="javascript:void(0);" class="edt">2837#</a>
									</td>
									<td>3/4/2017<br/>2:39PM</td>
									<td>
										<select class="form-control newSelect" style="min-width:260px;">
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
						$params = array_merge(['user/report-delete'], ['id' => $report->id]);
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
					<div class="form-group customBtnset align-left no-gap">
						<a href="<?php 
						$params = array_merge(['user/view'], ['id' => $individualMyAccountForm->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-default btn-sm">Cancel</a>
						<input type="submit" value="Save" class="btn btn-primary btn-sm">
						
						<a href="<?php 
						$params = array_merge(['user/delete'], ['id' => $individualMyAccountForm->id]);
						echo Yii::$app->UrlManager->createUrl($params); ?>" class="btn btn-danger btn-sm" data-confirm="Are you sure you want to delete this item?" data-method="post" data-pjax="0">Delete</a>
						
					</div>
				</div>
			<?php ActiveForm::end(); ?>
<?php 
$js = <<<JS
$(document).ready(function() {
		$('#individualmyaccountform-user_image').change( function(event) {
            var tmppath = URL.createObjectURL(event.target.files[0]);
            $("#sel_img").fadeIn("fast").attr('src',tmppath);			
            });
        });
JS;
$this->registerJs($js);
?>