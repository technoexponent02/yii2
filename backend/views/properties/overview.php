<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

use common\models\Reports;
use yii\helpers\Url;
use common\models\PropertyCategoryTranslation;
use common\components\SiteHelpers;
// use miloschuman\highcharts\Highcharts;

use yii\web\View;


$property_category = PropertyCategoryTranslation::find()->where(['locale' => Yii::$app->language])->all();

// if (count($property_category) > 0) 
// {
//     foreach ($property_category as $ckey => $category) 
//     {
//         VarDumper::dump($category->propertyTypeTranslations);
//     }
// }
//die;
// VarDumper::dump($property_category);


/* @var $this yii\web\View */
/* @var $searchModel backend\models\ModeratorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Properties Overview';
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    #container {
    min-width: 310px;
    max-width: 800px;
    height: 400px;
    margin: 0 auto
}
</style>
<div class="whitebox">
                <h2 class="secTl">Post Records</h2>
                <div class="preformanceChart">
                     <!-- <img src="<?php //echo Yii::$app->frontendUrlManager->createUrl(['frontend_assets/images/dashboard-image3.jpg']); ?>" alt="">  -->
                   
                     <div id="container"></div>
                </div>
            </div>
            <h2 class="secTl">Posted Properties Monthly Records</h2>
            <div class="whitebox no-padding">
                <div class="postOvrvwTblCont">
                    <div class="ovrvwLeft">
                        <div class="ovrvwLeftBx">
                            <h2>Current Month Total</h2>
                            <span class="count blue"><?php echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(); ?></span>
                            <span class="countTxt">Properties</span>
                        </div>
                        <div class="ovrvwLeftBx">
                            <h2>Total</h2>
                            <span class="count"><?php echo SiteHelpers::totalPostedRentedPropertiesCount(); ?></span>
                            <span class="countTxt">Properties</span>
                        </div>
                    </div>  
                    <div class="ovrvwRight">        
                        <div class="recordsTable">
                            <?php 
                            if (count($property_category) > 0)
                            {
                                foreach ($property_category as $ckey => $category) {
                                    //VarDumper::dump($category->propertyTypeTranslations);
                                    $property_type = $category->propertyTypeTranslations;
                                  ?>
                                <div class="colM <?php echo $ckey == 1 ? "colM2" :"" ?>">
                                <span class="tl"><?php echo $category->name; ?></span>
                                <div class="recordstbl">
                                    <?php if (count($property_type) > 0) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <div class="list">
                                                <div class="txt"><?php echo $type->name; ?><span class="no"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType($category->category_id, $type->category_id);
                                                ?></span></div>
                                            </div>
                                            <?php 
                                        }
                                    } ?>
                                    
                                   
                                </div>
                            </div>
                                  <?php 
                                }
                            }
                            ?>                            
                           
                        </div>
                    </div>
                    <div class="spacer"></div>
                </div>
            </div>          
            
            <h2 class="secTl">Rented Propertie Monthly Records</h2>
            <div class="whitebox no-padding">
                <div class="postOvrvwTblCont">
                    <div class="ovrvwLeft">
                        <div class="ovrvwLeftBx">
                            <h2>Current Month Total</h2>
                            <span class="count blue"><?php echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(null, null, 0); ?></span>
                            <span class="countTxt">Properties</span>
                        </div>
                        <div class="ovrvwLeftBx">
                            <h2>Total</h2>
                            <span class="count"><?php echo SiteHelpers::totalPostedRentedPropertiesCount(0); ?></span>
                            <span class="countTxt">Properties</span>
                        </div>
                    </div>  
                    <div class="ovrvwRight">        
                        <div class="recordsTable">
                            <?php 
                            if (count($property_category) > 0)
                            {
                                foreach ($property_category as $ckey => $category) {
                                    //VarDumper::dump($category->propertyTypeTranslations);
                                    $property_type = $category->propertyTypeTranslations;
                                  ?>
                                  <div class="colM <?php echo $ckey == 1 ? "colM2" :"" ?>">
                                <span class="tl"><?php echo $category->name; ?></span>
                                <div class="recordstbl">
                                    <?php if (count($property_type) > 0) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <div class="list">
                                                <div class="txt"><?php echo $type->name; ?><span class="no"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType($category->category_id, $type->category_id, 0);
                                                ?></span></div>
                                            </div>
                                            <?php 
                                        }
                                    } ?>
                                    
                                   
                                </div>
                            </div>
                                  <?php 
                                }
                            }
                            ?>
                            <!-- <div class="colM">
                                <span class="tl">Residential Properties</span>
                                <div class="recordstbl">
                                    <div class="list">
                                        <div class="txt">Family Apartment <span class="no">6</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Singals Apartment <span class="no">7</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Floor <span class="no">8</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Duplex <span class="no">8</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Villa <span class="no">6</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Compound Apartment <span class="no">5</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Compound Duplex <span class="no">7</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Building <span class="no">8</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="colM colM2">
                                <span class="tl">Commercial Properties</span>
                                <div class="recordstbl">
                                    <div class="list">
                                        <div class="txt">Office <span class="no">5</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Compound <span class="no">6</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Store <span class="no">3</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Building <span class="no">4</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Office Building <span class="no">2</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Health Center <span class="no">3</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Tower <span class="no">3</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">School <span class="no">5</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Vehicle Repair Shop <span class="no">2</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Shopping Center <span class="no">5</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Land <span class="no">1</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Show Room <span class="no">6</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Gas Station <span class="no">4</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Warehouse <span class="no">7</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Labor Accommodation <span class="no">6</span></div>
                                    </div>
                                    <div class="list">
                                        <div class="txt">Esteraha <span class="no">8</span></div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="spacer"></div>
                </div>
            </div>          
            <!-- <div class="dashSearch dashSearch5col">
                <div class="dashSearchBot">
                    <div class="recDownload" dir="ltr">
                        <a href="javascript:void(0);">
                            <img src="../assets/images/double-arrow.svg" alt=""/>
                        </a>
                        <ul>
                            <li>
                                <span class="txtTl">Export as...</span>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <span class="red">PDF</span> PDF
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <span>XLS</span> XLS
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="rw">
                        <div class="colM srch">
                            <input type="text" class="form-control" name="" placeholder="Search ">
                        </div>
                        <div class="colM">
                            <select class="form-control">
                                <option>Status</option>
                                <option>Approved</option>
                                <option>Pending</option>
                                <option>Stopped</option>
                            </select>
                        </div>  
                        <div class="colM">
                            <input type="text" class="form-control date" name="" placeholder="Form">
                        </div>  
                        <div class="colM">
                            <input type="text" class="form-control date" name="" placeholder="To">
                        </div>          
                        <div class="colM">                          
                            <input type="submit" value="Search" class="btn btn-warning btn-sm" name="">
                        </div>
                    </div>
                </div>
            </div> -->    
            <?php $form = ActiveForm::begin([
                'action' => ['report'],
                'method' => 'post',
            ]); ?>
                <div class="dashSearch dashSearch5col">
                    <div class="dashSearchBot">
                        <div class="rw">

                              
                           
                            <div class="colM">
                                <input type="text" class="form-control date" name="from_date" placeholder="Form">
                                
                            </div>  
                            <div class="colM">
                                <input type="text" class="form-control date" name="to_date" placeholder="To">

                            </div>  
                                  
                            <div class="colM">   
                                <?= Html::submitButton('Search', ['class' => 'btn btn-warning btn-sm']) ?>                       

                            </div>
                        </div>
                    </div>
                </div>
           <?php ActiveForm::end(); ?>       
           
<?php 
$display_months = array();
$monthly_visitors =  array();
for ($i = 0; $i < 6; $i++) {
  $display_months[] = "'".date('F Y', strtotime("-$i month"))."'";
  $month = date('m', strtotime("-$i month"));
  $year = date('Y', strtotime("-$i month"));
  $monthly_posts_for_residential[] = SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(2, null, 1, $month, $year);
  $monthly_posts_for_residential_rented[] = SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(2, null, 0, $month, $year);
  $monthly_posts_for_commercial[] = SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(1, null, 1, $month, $year);
  $monthly_posts_for_commercial_rented[] = SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyType(1, null, 0, $month, $year);
}
$display_months = implode(",", array_reverse($display_months));
$monthly_posts_for_residential = implode(",", array_reverse($monthly_posts_for_residential));
$monthly_posts_for_residential_rented = implode(",", array_reverse($monthly_posts_for_residential_rented));
$monthly_posts_for_commercial = implode(",", array_reverse($monthly_posts_for_commercial));
$monthly_posts_for_commercial_rented = implode(",", array_reverse($monthly_posts_for_commercial_rented));
//VarDumper::dump($display_months);
//VarDumper::dump($monthly_visitors);
$this->registerJsFile('https://code.highcharts.com/highcharts.js');
$js = <<<JS
$(document).ready(function() {
        $('.date').datetimepicker({
            timepicker:false,
            format:'d-m-Y'
            //format:'Y-m-d'
        });
        $('.time').datetimepicker({
            datepicker:false,
            format:'h:i a',
            formatTime:'h:i a',
        });
    });
Highcharts.chart('container', {
    credits: {
          enabled: false
    },
    chart: {
        type: 'line'
    },
    title: {
        text: 'Properties'
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
            return this.series.name + ' for <b>' + this.x + '</b> is <b>' + this.y;
        }
    },

    series: [{
        name: 'Residential Property',
        color: '#0066FF',
        data: [${monthly_posts_for_residential}]
    }, {
        name: 'Rented Residential Property',
        color: '#4CCAF4',
        data: [${monthly_posts_for_residential_rented}]
    }, {
        name: 'Commercial Property',
        color: '#FCCE3F',
        data: [${monthly_posts_for_commercial}]
    }, {
        name: 'Rented Commercial Property',
        color: '#8EC640',
        data: [${monthly_posts_for_commercial_rented}]
    }]
});
JS;
$this->registerJs($js, View::POS_END);

