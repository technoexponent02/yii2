<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<form class="customForm">
                <h2 class="secTl">Admin Profile view</h2>
                <div class="whitebox" style="float: left; width: 100%;">
                    <div class="adminDashboardrowCont">
                        <div class="adminDashboardrow">
                            <div class="colm pull-right">
                                <div class="form-group">
                                    <label class="text-right fullblock">User ID</label>
                                    <div class="infoTxt"><?php echo $model->id; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock blue">Admin name</label>
                                    <div class="infoTxt"><?php echo $model->name; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock blue">Email</label>
                                    <div class="infoTxt"><?php echo $model->email; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock blue">Access</label>
                                    <div class="infoTxt"><?php echo $model->user_type; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock blue">Phone number</label>
                                    <div class="infoTxt"><?php echo $model->phone; ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="text-right fullblock">Sign Up date</label>
                                    <div class="infoTxt"><?php echo Yii::$app->formatter->format($model->created_at,'datetime'); ?></div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="spacer"></div>
                    <div class="botInfotxtx">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="text-right fullblock">Location</label>
                                    <div class="infoTxt"><?php echo $model->country_id>0 && $model->getAddedCountry()->one()!=null && $model->getAddedCountry()->one()->getTranslatateData() !=null? $model->getAddedCountry()->one()->getTranslatateData()->translated_country_name : "Not set"; 
                                    
                                    ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-right fullblock">Last Login</label>
                                    <div class="infoTxt"><?php echo $model->last_login !=null ? Yii::$app->formatter->format($model->last_login,'date') : 'Not set'  ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="text-right fullblock">Ip</label>
                                    <div class="infoTxt"><?php echo ($model->login_ip !=null) ? $model->login_ip : 'Not set'; ?></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-right fullblock">Browser</label>
                                    <div class="infoTxt"><?php echo ($model->user_browser !=null) ? $model->user_browser : 'Not set'; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 pull-right">
                                <div class="form-group">
                                    <label class="text-right fullblock">Device</label>
                                    <div class="infoTxt"><?php echo ($model->user_device !=null) ? $model->user_device : 'Not set'; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="spacer"></div>
                    
                    
                    
                    <div class="form-group customBtnset align-left no-gap">
                        <?php echo Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                       <!--  <a href="javascript:void(0);" class="btn btn-primary btn-sm">Edit</a> -->
                    </div>
                </div>
            </form>

