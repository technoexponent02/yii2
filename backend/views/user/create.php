<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ModeratorForm */

$this->title = 'Create Moderator';
$this->params['breadcrumbs'][] = ['label' => 'Moderator', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
    	<div class="box box-info">
    		<div class="box-header with-border">
                <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
            </div>
			<div class="moderator-create">
			    <?php echo $this->render('_form', [
			        'model' => $model,
			    ]); ?>
			</div>
		</div>
	</div>
</div>