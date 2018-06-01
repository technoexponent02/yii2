<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
use common\models\User;
use yii\web\View;
use yii\helpers\VarDumper;
use common\components\SiteHelpers;

$this->title = 'Dashboard';
?>
<style type="text/css">
    #container {
    min-width: 310px;
    max-width: 800px;
    height: 400px;
    margin: 0 auto
}
</style>
		<div class="ovrvwBoxRw">
        <div class="colm">
            <div class="whitebox no-padding">
                <div class="bx">
                    <h2 class="bxTl"><?= getDbLanguageText('Todays_posts') ?></h2>
                    <span class="bxNo"><?php echo $total_today_posted_properties; ?></span>
                    <table class="bxTable">
                        <tr>
                            <td><?= getDbLanguageText('Residential') ?></td>
                            <td><?php echo $total_today_posted_properties_residential; ?></td>
                        </tr>
                        <tr>
                            <td><?= getDbLanguageText('Commercial') ?></td>
                            <td><?php echo $total_today_posted_properties_commercial; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="colm">
            <div class="whitebox no-padding">
                <div class="bx">
                    <h2 class="bxTl"><?= getDbLanguageText('Average_Time_on_site') ?></h2>
                    <span class="bxNo"><?php echo SiteHelpers::getAverageTimeOnSite(); ?> </span>
                </div>
            </div>
        </div>
        <div class="colm">
            <div class="whitebox no-padding">
                <div class="bx">
                    <?php 
                        $month = date('m', time());
                        $year = date('Y', time());
                        ?>
                    <h2 class="bxTl"><?= getDbLanguageText('Todays_visitors') ?></h2>
                    <span class="bxNo"><?php                            
                            echo SiteHelpers::getNoVisitorMonthWise($month, $year, 24);
                            ?></span>
                    <table class="bxTable">
                        
                        <tr>
                            <td><?= getDbLanguageText('New_Visitors') ?></td>
                            <td><?php                            
                            echo SiteHelpers::getNoVisitorMonthWise($month, $year, 72);
                            ?></td>
                        </tr>
                        <tr>
                            <td><?= getDbLanguageText('Total_Of_Current_Month') ?></td>
                            <td><?php                            
                            echo SiteHelpers::getNoVisitorMonthWise($month, $year);
                            ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="colm">
            <div class="whitebox no-padding">
                <div class="bx">
                    <h2 class="bxTl"><?= getDbLanguageText('Live_Posts') ?></h2>
                    <span class="bxNo"><?php echo $total_live_properties; ?></span>
                    <table class="bxTable">
                        <tr>
                            <td><?= getDbLanguageText('of_all_types') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="whitebox">
        <h2 class="secTl"><?= getDbLanguageText('Visitors') ?></h2>
        <div class="preformanceChart">
        <?php //echo Html::img('@web/admin/images/dashboard-image2.jpg'); ?>
        <div id="container"></div>
        </div>
    </div>
    <div class="adminUsers">
        <div class="colm">
            <div class="hide"></div>
            <div class="whitebox no-padding">
                <h2 class="secTl no-gap"><a href="<?php echo Yii::$app->urlManager->createUrl(['properties']); ?>"><?= getDbLanguageText('Last_Posts') ?></a></h2>
                <div class="table-responsive">
                    <?php 
                    if ($new_posts != null)
                    {
                        ?>
                        <table class="usertable postsTable nohover-btn">
                        <?php
                        foreach ($new_posts as $key => $new_post) {
                            ?>
                            <tr>
                                <td><?php echo $new_post->id; ?></td>
                                <td><?php echo $new_post->user->name; ?></td>
                                <td><?php echo $new_post->propertyType->translatateData->name; ?></td>
                                <td><?php 
                                if ($new_post->status == 1)
                                    {
                                        echo  '<span class="btn btn-warning btn-xs">'.getDbLanguageText('Pending').'</span>';
                                    }
                                    else if($new_post->status == 2)
                                    {
                                        echo '<span class="btn btn-success btn-xs">'.getDbLanguageText('Approved').'</span>';
                                    } 
                                    else if($new_post->status == 3)
                                    {
                                        echo '<span class="btn btn-danger btn-xs">'.getDbLanguageText('Stopped').'</span>';
                                    } 
                                ?></td>
                            </tr>
                            <?php 
                        }
                        ?>
                        </table>
                        <?php 
                    }
                    ?>
                    
                        
                        
                    
                </div>					
            </div>					
        </div>
        <div class="colm">
            <div class="hide"></div>
            <div class="whitebox no-padding">
                <h2 class="secTl no-gap"><a href="<?php echo Yii::$app->urlManager->createUrl(['user']); ?>"><?= getDbLanguageText('New_Users') ?></a></h2>
                <div class="table-responsive">
                    <?php 
                    if ($new_users != null)
                    {
                        ?>
                        <table class="usertable postsTable nohover-btn">
                        <?php 
                        foreach ($new_users as $key => $new_user) {
                            ?>
                            <tr>
                                <td><?php echo $new_user->id ?></td>
                                <td><?php if ($new_user->user_type == 3) 
                                            {
                                                echo 'Individual';
                                            }
                                        else if ($new_user->user_type == 4) {
                                            echo 'Organization';
                                        }
                                        else if ($new_user->user_type == 5) {
                                            echo 'Organization Subuser';
                                        }
                                       ?></td>
                                <td>
                                    <?php 
                                     switch ($new_user->status) {
                                    case User::STATUS_ACTIVE:
                                        if ($new_user->pending_verification == 2)
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
                            </tr>
                            <?php 
                        }
                        ?>
                         </table>
                        <?php 
                    }
                    ?>                  
                        
                    
                   
                </div>					
            </div>					
        </div>
    </div>
<?php 
$display_months = array();
$monthly_visitors =  array();
for ($i = 0; $i < 6; $i++) {
  $display_months[] = "'".date('F Y', strtotime("-$i month"))."'";
  $month = date('m', strtotime("-$i month"));
  $year = date('Y', strtotime("-$i month"));
  $monthly_visitors[] = SiteHelpers::getNoVisitorMonthWise($month, $year);
}
$display_months = implode(",", array_reverse($display_months));
$monthly_visitors = implode(",", array_reverse($monthly_visitors));
//VarDumper::dump($display_months);
//VarDumper::dump($monthly_visitors);
$this->registerJsFile('https://code.highcharts.com/highcharts.js');
$js = <<<JS
Highcharts.chart('container', {
    credits: {
          enabled: false
    },
    chart: {
        type: 'line'
    },
    title: {
        text: 'Visitors'
    },
    xAxis: {
        categories: [${display_months}]
    },
    yAxis: {
        visible: false
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    tooltip: {
        formatter: function() {
            var index = this.series.data.indexOf(this.point);
            console.log(index);
            console.log(this.series.data);
            return 'Visitors for <b>' + this.x + '</b> is <b>' + this.y;
        }
    },

    series: [{
        name: 'Visitors',
        color: '#0066FF',
        data: [${monthly_visitors}]
    }]
});
JS;
$this->registerJs($js, View::POS_END);

