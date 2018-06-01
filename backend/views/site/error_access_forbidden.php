<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<?php /*
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Access forbidden The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>*/?>
<div class="notAllowedCont">
    <img src="<?php echo Yii::$app->frontendUrlManager->createUrl(['frontend_assets/images/lock.svg']); ?>" alt=""/>
    <span class="msg">!You are not allowed to be here</span>
</div>
