<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\VarDumper;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\SearchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Searches');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php /*
<div class="searches-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
   

    <p>
        <?= Html::a(Yii::t('app', 'Create Searches'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
*/?>

<h2 class="secTl">Searches</h2>


<div class="whitebox no-padding">
                <div class="postsTable statustbl table-responsive">  
<?php Pjax::begin(); ?> 
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'tableOptions' => ['class' => 'table tableRtl'],
        'dataProvider' => $dataProvider,
        'summary' => "",
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'created_at', 'label' => 'Date', 'format'=>'raw', 'value' => function($model){
                                    return Yii::$app->formatter->format($model->created_at,'datetime');
                            },
                            'enableSorting' => false 

            ],
            [
                'attribute' => 'search',
                'label' => 'Search',
                'value' =>  'search',
                'enableSorting' => false
            ],
            [
                'attribute' => 'type',
                'label' => 'Type',
                'value' =>  function($model)
                {

                    return ($model->type0 !=null)?$model->type0->translatateData->name : "(not set)";
                },
                'enableSorting' => false
            ],
            [
                'attribute' => 'price',
                'label' => 'Price',
                'value' =>  'price',
                'enableSorting' => false
            ],
            [
                'attribute' => 'rooms',
                'label' => 'Rooms',
                'value' =>  'rooms',
                'enableSorting' => false
            ],
            [
                'attribute' => 'baths',
                'label' => 'Baths',
                'value' =>  'baths',
                'enableSorting' => false
            ],
            [
                'attribute' => 'results',
                'label' => 'Results',
                'value' =>  'results',
                'enableSorting' => false
            ],
            //'id',
            //'results',
            //'baths',
            //'rooms',
            //'price',
            //'type',
            //'search:ntext',
           // 'created_at',
            // 'updated_at',

           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
</div>
</br>
<input id="delete_records"  type="submit" class="btn btn-warning btn-sm " value="Clear Records">

<?php 
$delete_url = Yii::$app->urlManager->createUrl('search/delete-records');
$js = <<<JS
$('#delete_records').on('click', function(){
     $.post( "$delete_url");
     window.location.reload();
});
JS;
$this->registerJs($js);
?>