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
AppAsset::register($this);
$asset = AppAsset::register($this);
$baseUrl = $asset->baseUrl;
//echo $baseUrl;exit;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="adminPage adminloginPage">
<?php $this->beginBody() ?>
<?php echo $content;?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
