<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    //public $sourcePath = '@bower/backend/';
    public $css = [
        //'admin/css/bootstrap.min.css',
        'admin/font-awesome-4.7.0/css/font-awesome.css',
        'admin/owl-carousel/owl.carousel.css',
        'admin/owl-carousel/owl.theme.css',
        'admin/date-time-picker/jquery.datetimepicker.css',
        'admin/css/style.css',
        'admin/css/admin-style.css',
        'admin/css/responsive.css',
        'admin/css/admin-responsive.css',
    ];
    public $js = [
         //'js/jquery-1.11.1.js',
         //'admin/js/bootstrap.min.js',
        'admin/js/custom.js',
        'admin/date-time-picker/jquery.datetimepicker.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
