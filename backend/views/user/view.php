<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;
use common\models\PropertyReports;
use common\models\Property;

$properties = Property::find()->where(['user_id' => $model->id])->andWhere(['IN', 'property.status', [1, 2, 3]])->all();
$property_reports = PropertyReports::find()->where(['user_id' => $model->id])->all();


/* @var $this yii\web\View */
/* @var $searchModel backend\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Individual Account View';
$this->params['breadcrumbs'][] = $this->title;
?>
<form class="customForm">
                <h2 class="secTl"><?php echo $this->title; ?></h2>
                <div class="whitebox">
                    <div class="adminDashboardrowCont">
                        <div class="adminDashboardrow">
                            <div class="colm">
                                <div class="adminUserCont">
                                    <div class="adminUserImage">
                                        <img src="<?php 
                                    $user_image = Yii::$app->frontendUrlManager->baseUrl.'/frontend_assets/images/default-user.svg';
                                    if ($model->user_image != null)
                                    {
                                        $user_image= Yii::$app->UrlManager->baseUrl.'/upload/user_image/'. $model->user_image;
                                    }
                                    echo $user_image ?>" alt=""/>
                                    </div>
                                    <div class="badgProgress">Badge progress<br/>%20</div>
                                </div>
                            </div>
                            <div class="colm">
                                <div class="form-group">
                                    <label class="text-right fullblock">User ID</label>
                                    <div class="infoTxt"><?php echo $model->id; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">User Name</label>
                                    <div class="infoTxt"><?php echo $model->first_name." ".$model->last_name; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Email</label>
                                    <div class="infoTxt"><?php echo $model->email; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Phone number</label>
                                    <div class="infoTxt"><?php echo $model->phone; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Status</label>
                                    <div class="infoTxt"><?php echo ($model->status !=null) ? $model->getUserStatus($model->status) : ""; ?></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- <div class="spacer"></div> -->
                    <div class="form-group">
                        
                        <?php if ($properties != null) 
                        {
                            ?>
                            <label class="text-right fullblock">Added Properties</label>
                            <div class="postsTable statustbl reportDet-table table-responsive"> 
                                <table class="table tableRtl table-border">
                                <tr>
                                    <th width="120">Post ID</th>
                                    <th width="120">Date</th>
                                    <th width="120">Type</th>
                                    <th width="120">Status</th>
                                    <th width="180">Reason</th>
                                </tr>
                                <?php 
                            foreach ($properties as $key => $value) {
                               ?>
                               <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt"><?php echo $value->id ?></a>
                                    </td>
                                    <td><?php echo Yii::$app->formatter->format($value->created_at,'datetime'); ?></td>
                                    <td><?php echo $value->propertyType->category; ?></td>
                                    <td>
                                        <?php if ($value->status == 1)
                                        {
                                            ?>
                                            <span class="btn btn-xs btn-warning">Pending</span>
                                            <?php 
                                        }
                                        else if ($value->status == 2)
                                        {
                                            ?>
                                            <span class="btn btn-xs btn-success">Approved</span>
                                            <?php 
                                        }
                                        else if ($value->status == 3)
                                        {
                                            ?>
                                            <span class="btn btn-xs btn-danger">Stopped</span>
                                            <?php 
                                        } ?>
                                        
                                    </td>
                                    <td><?= ($value->reports)? $value->reports->translatateData->name: 'NA'; ?></td>
                                </tr>
                               <?php 
                            }
                            ?>   
                                
                                <!-- <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt">2837#</a>
                                    </td>
                                    <td>3/4/2017<br/>2:39PM</td>
                                    <td>Compound</td>
                                    <td>
                                        <span class="btn btn-xs btn-danger">Stopepd</span>
                                    </td>
                                    <td>Low Resolution Picures</td>
                                </tr> -->
                            </table>

                            
                        </div>
                            <?php 
                        } ?>
                        
                    </div>
                    <div class="form-group">
                        
                            <?php 
                            if ($property_reports != null)
                            {
                                ?>
                                <label class="text-right fullblock">Reports</label>
                                <div class="postsTable statustbl reportDet-table table-responsive"> 
                                <table class="table tableRtl table-border">
                                    <tr>
                                        <th width="180">Post ID</th>
                                        <th width="180">Date</th>
                                        <th>Reason</th>
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
                    <div class="botInfotxtx">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-right fullblock">Location</label>
                                    <div class="infoTxt"><?php echo $model->country_id>0 && $model->getAddedCountry()->one()!=null && $model->getAddedCountry()->one()->getTranslatateData() !=null? $model->getAddedCountry()->one()->getTranslatateData()->translated_country_name : "Not set"; 
                                    
                                    ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-right fullblock">Last Login</label>
                                            <div class="infoTxt"><?php echo $model->last_login !=null ? Yii::$app->formatter->format($model->last_login,'date') : 'Not set'  ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-right fullblock">Sign Up date</label>
                                            <div class="infoTxt"><?php echo $model->created_at !=null ? Yii::$app->formatter->format($model->created_at,'date') : 'Not set'  ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-right fullblock">Ip</label>
                                    <div class="infoTxt"><?php echo ($model->login_ip !=null) ? $model->login_ip : 'Not set'; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 pull-left">
                                        <div class="form-group">
                                            <label class="text-right fullblock">Browser</label>
                                            <div class="infoTxt"><?php echo ($model->user_browser !=null) ? $model->user_browser : 'Not set'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 pull-right">
                                <div class="row">
                                    <div class="col-md-6 pull-left">
                                        <div class="form-group">
                                            <label class="text-right fullblock">Device</label>
                                            <div class="infoTxt"><?php echo ($model->user_device !=null) ? $model->user_device : 'Not set'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="spacer"></div> -->
                    
                    
                    
                    <div class="form-group customBtnset align-left no-gap">
                        <a href="<?php 
                        $params = array_merge(["user/update"], ["id" => $model->id]);
                        echo Yii::$app->urlManager->createUrl($params);?>" class="btn btn-primary btn-sm">Edit</a>
                    </div>
                </div>
            </form>
  
            

