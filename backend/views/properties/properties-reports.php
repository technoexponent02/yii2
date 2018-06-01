<?php
use common\components\SiteHelpers;
use common\models\PropertyCategoryTranslation;
$property_category = PropertyCategoryTranslation::find()->where(['locale' => Yii::$app->language])->all();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>PDF</title>
</head>
	
<body style="background:#777777; padding:0; margin:0;">
	<table style="width:700px; max-width:100%; margin:0 auto; font-family:Arial; margin:0 auto; font-size:16px; color:#29353b; background:#ffffff;" cellpadding="10" cellspacing="0">
		<tr>
			<td style="vertical-align:top; padding:12px 16px; background:#334e9f; text-align:right; ">
				<a href=""><img src="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['frontend_assets/images/logo.png']); ?>" alt="" style="display:inline-block; height:42px; width:auto;"/></a>
			</td>
		</tr>
		
		<tr>
			<td style="text-align:left; vertical-align:top; padding:0; color:#222222; font-size:12px; line-height:15px; font-weight:300;">
				<table style="width:100%; border-left:1px solid #dddddd; border-top:1px solid #dddddd;" cellpadding="6" cellspacing="0">
					<tr>
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<tr>
									<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;">from</td>
									<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;">to</td>
								</tr>	
								<tr>
									<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?=  date('d/m/Y', $from_date) ?></td>
									<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?= date('d/m/Y', $to_date) ?></td>
								</tr>	
							</table>
						</td>						
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<tr>
									<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;">&nbsp;</td>
									<td width="120" style="border-bottom:1px solid #ddd;">&nbsp;</td>
								</tr>	
								<tr>
									<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;">&nbsp;</td>
									<td width="120" style="border-bottom:1px solid #ddd;">&nbsp;</td>
								</tr>	
							</table>
						</td>
					</tr>
					<tr>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="4">Posted Properties Monthly Records</td>
					</tr>
					<tr>
						<?php 
	                    if (count($property_category) > 0)
	                    {
	                        foreach ($property_category as $ckey => $category) {

	                    ?>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="2"><?php echo $category->name; ?></td>
						<?php 
	                        }
	                    }
	                    ?>
					</tr>
					<tr>
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<?php 
				                    if (count($property_category) > 0)
				                    {
				                        foreach ($property_category as $ckey => $category) {
				                        	$property_type = $category->propertyTypeTranslations;

				                    ?>
				                    <?php if (count($property_type) > 0 && $category->category_id == 1) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <tr>
												<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php echo $type->name; ?></td>
												<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyTypeByDate($category->category_id, $type->category_id, 1, $from_date, $to_date);
                                                ?></td>
											</tr>
                                            <?php 
                                        }
                                    } ?>
									<?php 
				                        }
				                    }
	                    		?>
								
							</table>
						</td>


						
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<?php 
				                    if (count($property_category) > 0)
				                    {
				                        foreach ($property_category as $ckey => $category) {
				                        	$property_type = $category->propertyTypeTranslations;

				                    ?>
				                    <?php if (count($property_type) > 0 && $category->category_id == 2) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <tr>
												<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php echo $type->name; ?></td>
												<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyTypeByDate($category->category_id, $type->category_id, 1, $from_date, $to_date);
                                                ?></td>
											</tr>
                                            <?php 
                                        }
                                    } ?>
									<?php 
				                        }
				                    }
	                    		?>
							</table>
						</td>
					</tr>
					<tr>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="2">&nbsp;</td>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="2">&nbsp;</td>
					</tr>
					<!-- 2nd -->
					<tr>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="4">Rented Propertie Monthly Records</td>
					</tr>
					<tr>
						<?php 
	                    if (count($property_category) > 0)
	                    {
	                        foreach ($property_category as $ckey => $category) {

	                    ?>
						<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;" align="center" colspan="2"><?php echo $category->name; ?></td>
						<?php 
	                        }
	                    }
	                    ?>
					</tr>
					<tr>
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<?php 
				                    if (count($property_category) > 0)
				                    {
				                        foreach ($property_category as $ckey => $category) {
				                        	$property_type = $category->propertyTypeTranslations;

				                    ?>
				                    <?php if (count($property_type) > 0 && $category->category_id == 1) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <tr>
												<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php echo $type->name; ?></td>
												<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyTypeByDate($category->category_id, $type->category_id, 0, $from_date, $to_date);
                                                ?></td>
											</tr>
                                            <?php 
                                        }
                                    } ?>
									<?php 
				                        }
				                    }
	                    		?>
							</table>
						</td>


						
						<td valign="top" colspan="2" width="50%" style="padding:0;">
							<table style="width:100%;" cellpadding="6" cellspacing="0">
								<?php 
				                    if (count($property_category) > 0)
				                    {
				                        foreach ($property_category as $ckey => $category) {
				                        	$property_type = $category->propertyTypeTranslations;

				                    ?>
				                    <?php if (count($property_type) > 0 && $category->category_id == 2) {
                                        foreach ($property_type as $pkey => $type) {
                                            ?>
                                            <tr>
												<td style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php echo $type->name; ?></td>
												<td width="120" style="border-right:1px solid #ddd; border-bottom:1px solid #ddd;"><?php 
                                                echo SiteHelpers::getPropertiesMonthWiseTotalCountByCategoryOrPropertyTypeByDate($category->category_id, $type->category_id, 0, $from_date, $to_date);
                                                ?></td>
											</tr>
                                            <?php 
                                        }
                                    } ?>
									<?php 
				                        }
				                    }
	                    		?>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td bgcolor="#f1f2f2" style="text-align:left; vertical-align:top; padding:12px 16px 6px 16px; background:#f1f2f2;">
				<table style="width:100%;" cellpadding="5" cellspacing="0">
					<tr>
						<td style="text-align:left; vertical-align:middle;">
							<a href="" style="display:inline-block; vertical-align:top; margin:0 6px 0 0;">
								<img src="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['frontend_assets/images/pinterest-icon.png']); ?>" alt="" height="14"/>
							</a>
							<a href="" style="display:inline-block; vertical-align:top; margin:0 6px 0 0;">
								<img src="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['frontend_assets/images/twitter-icon.png']); ?>" alt="" height="14"/>
							</a>
							<a href="" style="display:inline-block; vertical-align:top; margin:0 6px 0 0;">
								<img src="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['frontend_assets/images/youtube-icon.png']); ?>" alt="" height="14"/>
							</a>
						</td>
						<td style="text-align:left; vertical-align:middle;" width="130">
							<a href=""><img src="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['frontend_assets/images/logo.png']); ?>" alt="" style="display:inline-block; height:34px; width:auto;"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="background:#e6e7e9; text-align:center; vertical-align:top; padding:14px 16px 10px 16px; color:#334FA0; font-size:8px; line-height:14px; font-weight:600; letter-spacing:7px;">
				<a href="" style="color:#334FA0; text-decoration:none;">WWW.WAARF.COM.SA</a>
			</td>
		</tr>
	</table>
</body>
</html>