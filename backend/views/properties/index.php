<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

use common\models\Reports;
use yii\helpers\Url;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Properties';
$this->params['breadcrumbs'][] = $this->title;
?>

           <!--  <div class="headbtns align-right">
                <a href="<?php //echo Yii::$app->UrlManager->createUrl('seodata/create'); ?>" class="btn btn-sm btn-primary">Create</a>
            </div> -->
            
            <?php Pjax::begin(); ?> 
           <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>
                <div class="dashSearch dashSearch5col">
                    <div class="rw">
                        <div class="colM srch">
                            <!-- <input type="text" class="form-control" name="" placeholder="Search "> -->
                            <?php echo $form->field($searchModel, 'id')->textInput()->input('text', ['placeholder' => 'Search By Post ID'])->label(false); ?>
                        </div>
                        <!-- <div class="colM">
                            <?php //echo $form->field($searchModel, 'user_id')->textInput()->input('text', ['placeholder' => 'Search By User name'])->label(false); ?>
                        </div> -->
                          
                        <div class="colM">
                            <?php echo $form->field($searchModel, 'status')->dropDownList([''=> 'Select Status' ,'1' => 'Pending', '2' => 'Approved', '3' => 'Nothing' ])->label(false); ?>
                        </div> 
                        <div class="colM">
                            <!-- <input type="text" class="form-control date" name="" placeholder="Form"> -->
                            <?php echo $form->field($searchModel, 'from_date')->textInput()->input('text', ['class' => 'form-control date', 'placeholder' => 'From'])->label(false); ?>
                        </div>  
                        <div class="colM">
                            <!-- <input type="text" class="form-control date" name="" placeholder="To"> -->
                            <?php echo $form->field($searchModel, 'to_date')->textInput()->input('text', ['class' => 'form-control date', 'placeholder' => 'To'])->label(false); ?>
                        </div>  
                              
                        <div class="colM">   
                            <?= Html::submitButton('Search', ['class' => 'btn btn-warning btn-sm']) ?>                       

                        </div>
                    </div>
                </div>
           <?php ActiveForm::end(); ?>
            <div class="whitebox no-padding">
                 <?php 
                $gridColumns = [
                    [
                            'attribute' => 'id',
                            'label' => 'Post ID',
                            'format' => 'raw',
                            'value' =>  function ($model)
                            {                                
                                return $model->id;
                            },
                            'enableSorting' => false 
                        ], 
                     [
                            'attribute' => 'user_id',
                            'label' => 'User',
                            'value' =>  'user.name',
                            'enableSorting' => false 
                        ],
                        [
                            'attribute' => 'property_type',
                            'label' => 'Type',
                            'value' =>  'propertyType.translatateData.name',
                            'enableSorting' => false 
                        ], 
                         ['attribute' => 'status', 'label' => 'Status', 'format'=>'raw', 'value' => function($model){
                                    if ($model->status == 1)
                                    {
                                        return 'Pending';
                                    }
                                    else if($model->status == 2)
                                    {
                                        return 'Approved';
                                    } 
                                    else if($model->status == 3)
                                    {
                                        return 'Stopped';
                                    } 
                            }
                            , ],  
                        //'approvedBy.name',
                         ['attribute' => 'created_at', 'label' => 'Date', 'format'=>'raw', 'value' => function($model){
                                    return Yii::$app->formatter->format($model->created_at,'datetime');
                            }], 
                    //['class' => 'yii\grid\ActionColumn'],
                ];

                /*echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'fontAwesome' => true,
                    'showColumnSelector' => false,
                    'exportConfig' => [
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_EXCEL => false,
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_EXCEL_X => false,

            ],
                ]);*/
                ?>
                <div class="postsTable statustbl table-responsive">  
                
               
                <?php echo GridView::widget([
                    'tableOptions' => ['class' => 'table tableRtl'],
                    'dataProvider' => $dataProvider,
                    'summary' => "",
                    //'filterModel' => $searchModel,
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],

                        [
                            'attribute' => 'id',
                            'label' => 'Post ID',
                            'format' => 'raw',
                            'value' =>  function ($model)
                            {
                                $current_user = Yii::$app->user->identity;
                                $post_url = Yii::$app->frontendUrlManager->createUrl(['post/view-post','id' => $model->id, 'access' => base64_encode($current_user->user_type)]);
                                return '<a href="'.$post_url.'" class="edt">'.$model->id.'</a>';
                            },
                            'enableSorting' => false 
                        ], 
                        //'user.name', 
                        [
                            'attribute' => 'user_id',
                            'label' => 'User',
                            'value' =>  'user.name',
                            'enableSorting' => false 
                        ],
                        [
                            'attribute' => 'property_type',
                            'label' => 'Type',
                            'value' =>  'propertyType.translatateData.name',
                            'enableSorting' => false 
                        ], 
                        
                        ['attribute' => 'status', 'label' => 'Status', 'format'=>'raw', 'value' => function($model){
                                    if ($model->status == 1)
                                    {
                                        return '<span class="btn btn-warning btn-xs">Pending</span>';
                                    }
                                    else if($model->status == 2)
                                    {
                                        return '<span class="btn btn-success btn-xs">Approved</span>';
                                    } 
                                    else if($model->status == 3)
                                    {
                                        return '<span class="btn btn-danger btn-xs">Stopped</span>';
                                    } 
                            }
                            ,
                        'enableSorting' => false ],  
                        //'approvedBy.name',
                         ['attribute' => 'created_at', 'label' => 'Date', 'format'=>'raw', 'value' => function($model){
                                    return Yii::$app->formatter->format($model->created_at,'datetime');
                            },
                        'enableSorting' => false ],                 
                        ['attribute' => 'status', 'label' => 'Action', 'format' => 'raw', 'value' => function($model){
                            return '<div class="custRdolist">
                                    <label>
                                        <input type="radio" class="actionStatus" name="status_'.$model->id.'[]" value="2" '.(
                                            $model->status == 2 ? "checked" : "").'>
                                        <span class="ico"></span>
                                        Approve
                                    </label>
                                    <br/>
                                    <label>
                                        <input type="radio" class="actionStatus" name="status_'.$model->id.'[]" value="3" '.(
                                            $model->status == 3 ? "checked" : "").'>
                                        <span class="ico"></span>
                                        Stop
                                    </label>
                                    <br/>
                                    <label>
                                        <input type="radio" class="actionStatus" name="status_'.$model->id.'[]" value="1" '.(
                                            $model->status == 1 ? "checked" : "").'>
                                        <span class="ico"></span>
                                        Nothing
                                    </label>
                                </div>';
                        },
                    'enableSorting' => false ],
                        ['contentOptions' => function($model){
                                $show_reason = $model->status == 3 ? "on" : "greyBg";
                                return ['class' => $show_reason];
                            } ,'attribute' => 'report_id', 'label' => 'Reason', 'format' => 'raw', 'value' => function($model){
                                //$show_reason = $model->status == 3 ? "on" : "greyBg";
                                $reason_list = ArrayHelper::map(Reports::getCategoryDropdownList(), 'id', 'category');
                                unset($reason_list[4]);
                                $reason_list_html = "";
                                foreach ($reason_list  as $rkey => $reason) {
                                    $selected = ($rkey == $model->reason_id) ? 'selected' : ' ';
                                     $reason_list_html .= '<option value ="'.$rkey.'"'. $selected. '>'.$reason.'</option>';
                                 } 
                                return '<div style="display:inline-block; min-width:160px;" >
                                    <select class="form-control newSelect reasonSelect">                                      
                                        '.$reason_list_html.'
                                    </select>
                                </div>';
                            }
                            ,
                        'enableSorting' => false ],
                        [
                          'class' => 'yii\grid\ActionColumn',
                          'header' => 'Save',
                          'headerOptions' => ['style' => 'color:#337ab7'],
                          'template' => '{update}',
                          'buttons' => [
                            'update' => function ($url, $model) {
                                return '<a href="javascript:void(0);" class="btn btn-primary btn-xs btnpropertyCheck" style="min-width:50px;" data-id="'.$model->id.'">Save</a>';
                            },                           

                          ]
                          
                          ],
                              
                      [
                            'attribute' => 'approved_by',
                            'label' => 'Checked By',
                            'value' =>  'approvedBy.name',
                            'enableSorting' => false 
                        ],
                      
                    ],
                ]); ?>                 
                 
                </div>
            </div>
            <?php Pjax::end(); ?>  
<?php 
$updated_post_status_url = Url::to(['properties/update'], true);
$js = <<<JS
$(document).ready(function() {
    $('.date').datetimepicker({
            timepicker:false,
            //format:'d-m-y'
            format:'Y-m-d'
        });
        $('.time').datetimepicker({
            datepicker:false,
            format:'h:i a',
            formatTime:'h:i a',
        });
        $(document).on("click",".custRdolist input", function(){
            var thisVal = $(this).val();
            //console.log(thisVal);
            if(thisVal == 3){
                //console.log(thisVal);
                $(this).parent().parent().parent().parent().children(".greyBg").addClass("on").removeClass("greyBg");
            }else{
                 $(this).parent().parent().parent().parent().children(".on").addClass("greyBg").removeClass("on");
            }
        });
        $(document).on("click",".btnpropertyCheck", function(e){
            var el = $(this);
            var id = el.attr('data-id');

            var status = el.parent().parent().find( "input:radio.actionStatus:checked" ).val();

            
            var reason_id = 0;
            if (status == 3)
            {
                reason_id = el.parent().parent().find( ".reasonSelect option:selected" ).val();
                console.log(reason_id);

            }
            //console.log(status);
            //console.log(reason_id);
            //console.log(id);
            var update_url= "$updated_post_status_url?id="+ id;
            //console.log(update_url);
            //return false;
             $.post(update_url,
                {
                    status: status,
                    reason_id: reason_id
                },
                function(data, status){
                    /*alert("Data: " + data + "\nStatus: " + status);*/
                    //console.log(data + " " +status);
                    if (status == "success")
                    {
                        window.location.reload(true);
                    }
                });

        });
    });
JS;
$this->registerJs($js);