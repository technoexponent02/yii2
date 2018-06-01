<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
/*use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
*/
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
//echo $baseUrl;exit;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="rtl">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>Admin | <?php echo Html::encode($this->title); ?></title>
    <?php $this->head() ?>
</head>
<body class="adminPage">
<?php $this->beginBody() ?>

<?php echo $this->render('header',['baseUrl' => $baseUrl]);?>
<!-- <div class="wrapper"> -->
<div class="adminBody">    
    <?php echo $this->render('leftmenu',['baseUrl' => $baseUrl]);?>
    <?php echo $this->render('content',['content' => $content,'baseUrl' => $baseUrl]);?>
    <?php //echo $this->render('footer',['baseUrl' => $baseUrl]);?>
    <?php //echo $this->render('rightside',['baseUrl' => $baseUrl]);?>
    <!-- <div class="control-sidebar-bg"></div> -->
    <div class="spacer"></div>
    </div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
