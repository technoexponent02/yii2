<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AdminsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="headbtns align-right">
                <!-- <a href="javascript:void(0);" class="btn btn-sm btn-primary">Add New Admin</a> -->
                <?php echo  Html::a(Yii::t('app', 'Add New Admin'), ['create'], ['class' => 'btn btn-sm btn-primary']) ?>
            </div>

<div class="whitebox no-padding">
    <div class="postsTable statustbl table-responsive">   
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo  Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'tableOptions' => ['class' => 'table tableRtl'],
        'dataProvider' => $dataProvider,
        'summary' => "",
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'id',
                'label' => 'User ID',
                'value' =>  'id',
                'enableSorting' => false
            ],
            //'name',
            [
                'attribute' => 'name',
                'label' => 'Name',
                'value' =>  'name',
                'enableSorting' => false
            ],
            //'email',
            [
                'attribute' => 'email',
                'label' => 'Email',
                'value' =>  'email',
                'enableSorting' => false
            ],
            //'phone',
            [
                'attribute' => 'phone',
                'label' => 'Phone Number',
                'value' =>  'phone',
                'enableSorting' => false
            ],
            //'user_type',
            [
                'attribute' => 'user_type',
                'label' => 'Access',
                'value' =>  function($model){
                    switch ($model->user_type) {
                        case 1:
                            return "Super Admin";
                            break;
                        case 2:
                            return "Admin";
                            break;
                        case 7:
                            return "Supervisor";
                            break;
                        default:
                            # code...
                            break;
                    }
                },
                'enableSorting' => false
            ],
             // [
             //                'attribute' => 'status',
             //                'label' => 'Status',
             //                'format'=>'raw', 
             //                'value' => function($model){
             //                    switch ($model->status) {
             //                        case User::STATUS_ACTIVE:
             //                            //return 'Activated'; 
             //                            // if ($model->user_type == 3)
             //                            // {
             //                            //     return 'Verified';
             //                            // }
             //                            // else
             //                            // {
             //                            //    return 'Activated'; 
             //                            // }  
             //                            if ($model->pending_verification == 2)
             //                            {
             //                                return '<span class="btn btn-default btn-xs">Inactivated</span>';

             //                            }
             //                            else
             //                            {
             //                               return '<span class="btn btn-primary btn-xs">Activated</span>'; 

             //                            }                                          
             //                            break;
             //                        case User::STATUS_UNACTIVATED:
             //                           return '<span class="btn btn-default btn-xs">Inactivated</span>';
             //                           break;
             //                        case User::STATUS_REQUESTED:
             //                            return 'Requested'; 
             //                            break;
             //                        case User::STATUS_BANNED:
             //                            return '<span class="btn btn-danger btn-xs">Banned</span>';
             //                            break;                                        
             //                        case User::STATUS_BLOCKED:
             //                            //return 'Blocked'; 
             //                            return '<span class="btn btn-danger btn-xs">Blocked</span>';
             //                            break; 
             //                        case User::STATUS_DELETED:
             //                            //return 'Deleted';  
             //                            return '<span class="btn btn-danger btn-xs">Deleted</span>';
             //                            break;    
             //                        // case 5:
             //                        //     return 'Organization Subuser';
             //                        //     break;                                   
             //                        default:
             //                            return 'Not Set';
             //                            break;
             //                    }
             //                },
             //            ],
            // 'organization_name',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            // 'verification_code',
            // 'otp_request',
            // 'email:email',
            // 'parent_id',
            // 'user_type',
            // 'phone',
            // 'usr_lat',
            // 'usr_lng',
            // 'sign_up_ip',
            // 'login_ip',
            // 'last_login',
            // 'account_balance',
            // 'user_image',
            // 'country_id',
            // 'preferred_locale',
            // 'reason:ntext',
            // 'user_device',
            // 'user_browser',
            // 'is_badge',
            // 'is_online',
            // 'status',
            // 'pending_verification',
            // 'created_at',
            // 'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
            [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'View',
                          'headerOptions' => ['style' => 'color:#337ab7'],
                          'template' => '{view}',
                          'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('View', $url, [
                                            'title' => Yii::t('app', 'View'),
                                ]);
                            },                           

                          ],
                          
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
</div>
