<?php 
use common\models\User;
//in_array(Yii::$app->controller->action->id,['create']) ?>
<aside class="adminSidebar">
			<ul>
				<?php  if(Yii::$app->user->can(User::ROLE_ADMIN))
		        {
		        ?>
				<li>
					<a href="<?php echo Yii::$app->urlManager->createUrl(['/']);?>" <?php echo in_array(Yii::$app->controller->id,['site'])?' class="sl" ':'';?>><?= getDbLanguageText('Overview') ?></a>
				</li>
				<li>
					<a href="javascript:void(0);" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl open" ':'';?>><?= getDbLanguageText('Posts_overview') ?></a>
					<ul <?php echo in_array(Yii::$app->controller->id,['properties'])?' style="display: block;" ':'';?>>
						<li>
							<a href="<?php echo Yii::$app->urlManager->createUrl(['properties/overview']);?>" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl" ':'';?>><?= getDbLanguageText('Posts_overview') ?></a>
						</li>
						<li>
							<a href="<?php echo Yii::$app->urlManager->createUrl(['properties']);?>" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl" ':'';?>><?= getDbLanguageText('Posts_approval') ?></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="<?php echo Yii::$app->urlManager->createUrl(['user']);?>" <?php echo in_array(Yii::$app->controller->id,['user'])?' class="sl" ':'';?>><?= getDbLanguageText('Users_management') ?></a>
				</li>
				<li>
            <a href="<?php echo Yii::$app->urlManager->createUrl(['property-reports']);?>" <?php echo in_array(Yii::$app->controller->id,['property-reports'])?' class="sl" ':'';?>><?= getDbLanguageText('Reports') ?></a>
				</li>
				<li>
						<a href="<?php echo Yii::$app->urlManager->createUrl(['admins']);?>" <?php echo in_array(Yii::$app->controller->id,['admins'])?' class="sl" ':'';?>>Admin Management</a>
				</li>
				<?php 
				}
				?>
				<?php  if(Yii::$app->user->can(User::ROLE_QUALITY_TEAM) || Yii::$app->user->can(User::ROLE_SUPERVISOR))
		        {
		        ?>
				<li>
					<a href="javascript:void(0);" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl open" ':'';?>><?= getDbLanguageText('Posts_overview') ?></a>
					<ul <?php echo in_array(Yii::$app->controller->id,['properties'])?' style="display: block;" ':'';?>>
						<li>
							<a href="<?php echo Yii::$app->urlManager->createUrl(['properties/overview']);?>" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl" ':'';?>><?= getDbLanguageText('Posts_overview') ?></a>
						</li>
						<li>
							<a href="<?php echo Yii::$app->urlManager->createUrl(['properties']);?>" <?php echo in_array(Yii::$app->controller->id,['properties'])?' class="sl" ':'';?>><?= getDbLanguageText('Posts_approval') ?></a>
						</li>
					</ul>
				</li>
				<li>
					<a href="<?php echo Yii::$app->urlManager->createUrl(['user']);?>" <?php echo in_array(Yii::$app->controller->id,['user'])?' class="sl" ':'';?>><?= getDbLanguageText('Users_management') ?></a>
				</li>
				<li>
            <a href="<?php echo Yii::$app->urlManager->createUrl(['property-reports']);?>" <?php echo in_array(Yii::$app->controller->id,['property-reports'])?' class="sl" ':'';?>><?= getDbLanguageText('Reports') ?></a>
				</li>
				<li>
						<a href="<?php echo Yii::$app->urlManager->createUrl(['admins']);?>" <?php echo in_array(Yii::$app->controller->id,['admins'])?' class="sl" ':'';?>>Admin Management</a>
				</li>
				<?php 
				}
				?>
				<?php  if(Yii::$app->user->can(User::ROLE_ADMIN))
		        {
		        ?>
				<li>
					   <a href="<?php echo Yii::$app->urlManager->createUrl(['search']);?>" <?php echo in_array(Yii::$app->controller->id,['search'])?' class="sl" ':'';?>><?= getDbLanguageText('Searches') ?></a>

				</li>
				<li>
					<a href="<?php echo Yii::$app->urlManager->createUrl(['seodata']);?>" <?php echo in_array(Yii::$app->controller->id,['seodata'])?' class="sl" ':'';?>>SEO</a>
				</li>
				<li>
            <a href="<?php echo Yii::$app->urlManager->createUrl(['ads/update']);?>" <?php echo in_array(Yii::$app->controller->id,['ads'])?' class="sl" ':'';?>>Ad</a>
				</li>
				<li>
            <a href="<?php echo Yii::$app->urlManager->createUrl(['email-templates']);?>" <?php echo in_array(Yii::$app->controller->id,['email-templates'])?' class="sl" ':'';?>>Emails</a>
				</li>
				<li>
            <a href="<?php echo Yii::$app->urlManager->createUrl(['user/change-password']);?>" <?php echo in_array(Yii::$app->controller->action->id,['change-password'])?' class="sl" ':'';?>>Change Password</a>
				</li>
				<?php 
			}
				?>
			</ul>
		</aside>