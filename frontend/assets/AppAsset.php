<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'frontend_assets/css/bootstrap.min.css',
        'frontend_assets/font-awesome-4.7.0/css/font-awesome.css',
        'frontend_assets/owl-carousel/owl.carousel.css',
        'frontend_assets/owl-carousel/owl.theme.css',
        'frontend_assets/css/style.css',
        'frontend_assets/css/responsive.css',
        'frontend_assets/date-time-picker/jquery.datetimepicker.css',
    ];
    public $js = [
        'frontend_assets/js/bootstrap.min.js',
        'frontend_assets/js/custom.js',
        'frontend_assets/date-time-picker/jquery.datetimepicker.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
