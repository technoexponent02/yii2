<?php
use yii\helpers\Html;
use yii\helpers\Vardumper;
use common\components\LanguageSwitcher;
$user=Yii::$app->user->identity;

//Vardumper::dump($user);
?>
<header class="adminHeader">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 menucol">
					<ul class="adminHeadlinks">
            <li><?php echo LanguageSwitcher::widget(); ?></li>
						<li><a href="javascript:void(0);"><?php echo $user->name; ?></a></li>
						<li><?php echo Html::beginForm(['/site/logout'], 'post') . Html::submitButton(getDbLanguageText('Logout'),['class' => 'btn']) . Html::endForm();?></li>
					</ul>
				</div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 logocol clearfix text-right">
					<div class="logo">
						<a href="#">
							<img src="<?php echo Yii::$app->frontendUrlManager->createUrl(['frontend_assets/images/logo.svg']); ?>" class="img-responsive">
						</a>
					</div>
				</div>
			</div>
		</div>
	</header>