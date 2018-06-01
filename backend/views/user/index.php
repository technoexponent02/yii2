<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>

            <div class="headbtns align-right">
                <a href="<?php echo Yii::$app->frontendUrlManager->baseUrl."/site/register" ?>" class="btn btn-sm btn-primary">Create New Account</a>
            </div>
            
            <?php Pjax::begin(); ?> 
            <?php echo  $this->render('_search', ['model' => $searchModel]); ?>
            <div class="whitebox no-padding">
                <div class="postsTable statustbl table-responsive">  
                
                
                <?php echo GridView::widget([
                    'tableOptions' => ['class' => 'table tableRtl'],
                    'dataProvider' => $dataProvider,
                    'summary' => "",
                   /* 'filterModel' => $searchModel,*/
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],

                        [   
                            'attribute' => 'id',
                            'label' => 'User ID',
                            'format'=>'raw', 
                            'value' => function($model){
                                return $model->id;
                            },
                        ],
                        //'email:email',
                        [   
                            'attribute' => 'name',
                            'label' => 'User Name',
                            'format'=>'raw', 
                            'value' => function($model){
                                return $model->name;
                            },
                        ],
                        [   
                            'attribute' => 'phone',
                            'label' => 'Phone',
                            'format'=>'raw', 
                            'value' => function($model){
                                return $model->phone;
                            },
                        ],
                        // 'status',
                        [
                            'attribute' => 'status',
                            'label' => 'Status',
                            'format'=>'raw', 
                            'value' => function($model){
                                switch ($model->status) {
                                    case User::STATUS_ACTIVE:
                                        //return 'Activated'; 
                                        // if ($model->user_type == 3)
                                        // {
                                        //     return 'Verified';
                                        // }
                                        // else
                                        // {
                                        //    return 'Activated'; 
                                        // }  
                                        if ($model->pending_verification == 2)
                                        {
                                            return '<span class="btn btn-default btn-xs">Inactivated</span>';

                                        }
                                        else
                                        {
                                           return '<span class="btn btn-primary btn-xs">Activated</span>'; 

                                        }                                          
                                        break;
                                    case User::STATUS_UNACTIVATED:
                                       return '<span class="btn btn-default btn-xs">Inactivated</span>';
                                       break;
                                    case User::STATUS_REQUESTED:
                                        return 'Requested'; 
                                        break;
                                    case User::STATUS_BANNED:
                                        return '<span class="btn btn-danger btn-xs">Banned</span>';
                                        break;                                        
                                    case User::STATUS_BLOCKED:
                                        //return 'Blocked'; 
                                        return '<span class="btn btn-danger btn-xs">Blocked</span>';
                                        break; 
                                    case User::STATUS_DELETED:
                                        //return 'Deleted';  
                                        return '<span class="btn btn-danger btn-xs">Deleted</span>';
                                        break;    
                                    // case 5:
                                    //     return 'Organization Subuser';
                                    //     break;                                   
                                    default:
                                        return 'Not Set';
                                        break;
                                }
                            },
                        ],
                        [
                            'attribute' => 'user_type',
                            'label' => 'Account Type',
                            'format'=>'raw', 
                            'value' => function($model){
                                switch ($model->user_type) {
                                    case 3:
                                        return 'Individual';
                                        break;
                                    case 4:
                                        return 'Organization';
                                        break;
                                    case 5:
                                        return 'Organization Subuser';
                                        break;                                   
                                    default:
                                        return 'Customer';
                                        break;
                                }
                            },
                        ],
                        'reason',
                        // [   
                        //     'attribute' => 'created_at',
                        //     'label' => 'Join On',
                        //     'format'=>'raw', 
                        //     'value' => function($model){
                        //         return date('d M Y',$model->created_at);
                        //     },
                        // ],

                        // ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
                         [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'Details',
                          'headerOptions' => ['style' => 'color:#337ab7'],
                          'template' => '{view}',
                          'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('Details', $url, [
                                            'title' => Yii::t('app', 'Details'),
                                ]);
                            },                           

                          ],
                          
                          ],
                    ],
                ]); ?>
                 
                   <!--  <table class="table tableRtl">
                        <tbody><tr>
                            <th width="130px">User ID</th>
                            <th width="190px">User Name</th>
                            <th width="140px">Status</th>
                            <th width="160px">Account Type</th>
                            <th width="160px">Reasson</th>
                            <th width="100px">Details</th>
                        </tr>

                        <tr>
                            <td>#8374</td>
                            <td>First And Last Name</td>
                            <td>
                                <span class="btn btn-primary btn-xs">Activiated</span>
                            </td>
                            <td>Organization Subuser</td>
                            <td>&nbsp;</td>
                            <td><a href="javascript:void(0);" class="edt">Details</a></td>
                        </tr>
 
                    </tbody></table> -->
                </div>
            </div>
            <?php Pjax::end(); ?>  
