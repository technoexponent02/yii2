<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;
use common\models\PropertyReports;
use common\models\Property;
// use yii\jui\AutoComplete;
// use yii\helpers\ArrayHelper;
// use common\models\CountryTranslation;

// $countries = CountryTranslation::find()
//             ->select(['country_id as value', 'translated_country_name as label'])
//             ->where(['locale' => Yii::$app->language])
//             ->asArray()
//             ->all();
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$properties = Property::find()->where(['parent_id' => $model->id])->andWhere(['IN', 'property.status', [1, 2, 3]])->all();
$property_reports = PropertyReports::find()->where(['user_id' => $model->id])->all();
$sub_users = User::find()->where(['parent_id' => $model->id ])->andWhere(['!=', 'status', User::STATUS_INCOMPLETE])->all();

$this->title = 'Organisation Account View';
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
                                    <?php 
                                    if ($model->is_badge == 10)
                                    {
                                    ?>
                                    <div class="chkIco">
                                        <img src="<?php echo Yii::$app->frontendUrlManager->baseUrl.'/frontend_assets/images/dashboard-check.svg'; ?>" alt="">
                                    </div>
                                    <?php 
                                    } ?>
                                </div>
                            </div>
                            <div class="colm">
                                <div class="form-group">
                                    <label class="text-right fullblock">User ID</label>
                                    <div class="infoTxt"><?php echo $model->id; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Organisation Name</label>
                                    <div class="infoTxt"><?php echo $model->organization_name; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Admin Name
                                        <?php 
                                        // echo AutoComplete::widget([
                                        //     'name' => 'country',
                                        //     'clientOptions' => [
                                        //         'source' => $countries,
                                        //     ],
                                        // ]);
                                        ?>
                                            
                                        </label>
                                    <div class="infoTxt"><?php echo $model->name; ?></div>
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
                                    <div class="infoTxt"><?php echo ($model->status !=null) ? ( 
                                        $model->pending_verification==2 ? "Inactivated" : $model->getUserStatus($model->status) ): ""; ?></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <!-- <div class="spacer"></div> -->
                    <div class="form-group">
                        
                        <!-- <div class="postsTable statustbl reportDet-table table-responsive">      
                            <table class="table tableRtl table-border">
                                <tr>
                                    <th width="120">Post ID</th>
                                    <th width="120">Date</th>
                                    <th width="120">Type</th>
                                    <th width="120">By</th>
                                    <th width="120">Status</th>
                                    <th width="180">Reason</th>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt">2837#</a>
                                    </td>
                                    <td>3/4/2017<br/>2:39PM</td>
                                    <td>Villa</td>
                                    <td>User ID</td>
                                    <td>
                                        <span class="btn btn-xs btn-success">Approved</span>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt">2837#</a>
                                    </td>
                                    <td>3/4/2017<br/>2:39PM</td>
                                    <td>Compound</td>
                                    <td>User ID</td>
                                    <td>
                                        <span class="btn btn-xs btn-danger">Stopepd</span>
                                    </td>
                                    <td>Low Resolution Picures</td>
                                </tr>
                            </table>
                        </div> -->
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
                        <!-- <label class="text-right fullblock">Reports</label>
                        <div class="postsTable statustbl reportDet-table table-responsive">      
                            <table class="table tableRtl table-border">
                                <tr>
                                    <th width="150">Post ID</th>
                                    <th width="150">Added By</th>
                                    <th width="150">Date</th>
                                    <th>Reason</th>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt">2837#</a>
                                    </td>
                                    <td>Subuser ID</td>
                                    <td>3/4/2017<br/>2:39PM</td>
                                    <td>Low Resolution Picures</td>
                                </tr>
                            </table>
                        </div> -->
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
                    <div class="form-group">
                        <?php 
                        if ($sub_users != null)
                        {
                            ?>
                            <label class="text-right fullblock">Subusers</label>
                            <div class="postsTable statustbl reportDet-table table-responsive">      
                            <table class="table tableRtl table-border">
                                <tr>
                                    <th width="150">User ID</th>
                                    <th width="150">Expiration In</th>                                    
                                    <th width="">User Name</th>
                                    <th width="150">Status</th>
                                    <th width="150">Activated Date</th>
                                    <th width="150">Password</th>
                                </tr>
                            <?php 
                            foreach ($sub_users as $key => $sub_user) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt"><?php echo $sub_user->id; ?></a>
                                    </td>
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
                                    <td><?php echo $sub_user->name; ?></td>
                                    <td>
                                        <?php 
                                     switch ($sub_user->status) {
                                    case User::STATUS_ACTIVE:
                                        if ($sub_user->pending_verification == 2)
                                        {
                                            echo  '<span class="btn btn-default btn-xs">Inactivated</span>';

                                        }
                                        else
                                        {
                                           echo  '<span class="btn btn-primary btn-xs">Activated</span>'; 

                                        }                                          
                                        break;
                                    case User::STATUS_UNACTIVATED:
                                       echo  '<span class="btn btn-default btn-xs">Inactivated</span>';
                                       break;
                                    case User::STATUS_REQUESTED:
                                        echo  'Requested'; 
                                        break;
                                    case User::STATUS_BANNED:
                                        echo  '<span class="btn btn-danger btn-xs">Banned</span>';
                                        break;                                        
                                    case User::STATUS_BLOCKED:
                                         echo  '<span class="btn btn-danger btn-xs">Blocked</span>';
                                        //echo  ''; 
                                        break; 
                                    case User::STATUS_DELETED:
                                        //echo  'Deleted';
                                        echo  '<span class="btn btn-danger btn-xs">Deleted</span>';  
                                        break;    
                                    // case 5:
                                    //     return 'Organization Subuser';
                                    //     break;                                   
                                    default:
                                        echo 'Not Set';
                                        break;
                                }
                                    ?>
                                    </td>
                                    <td><?php echo Yii::$app->formatter->format($sub_user->created_at,'datetime'); ?></td>
                                    <td>****</td>
                                </tr>
                                <?php 
                            }
                            ?>
                            </table>
                        </div>
                            <?php 
                        }
                        ?>
                        
                                
                                <!-- <tr>
                                    <td>
                                        <a href="javascript:void(0);" class="edt">8273#</a>
                                    </td>
                                    <td>First and Last Name</td>
                                    <td>
                                        <span class="btn btn-xs btn-primary">Active</span>
                                    </td>
                                    <td>3/4/2017<br/>2:39PM</td>
                                    <td>****</td>
                                </tr> -->
                            
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
                        $params = array_merge(["user/organisation-update"], ["id" => $model->id]);
                        echo Yii::$app->urlManager->createUrl($params);?>" class="btn btn-primary btn-sm">Edit</a>
                    </div>
                </div>
            </form>
  
            

