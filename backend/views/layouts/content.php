<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;
?>
<?php /* ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
    <section class="content-header">
    	<h1>
    		<?php echo $this->title;?>
    	<!-- <small>Control panel</small> -->
    	</h1>
    	<?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>
    <section class="content">
    	<?php echo $content;?>    	
    </section>
</div>
<?php */ ?>
<div class="adminLeftcont">
<?php echo $content;?>    	
		</div>