<?php
namespace frontend\modules\user\assets;

use frontend\assets\AppAsset;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class MainAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        "https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css",
    ];


    public $js = [
        "https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js",
    ];

    public $depends = [
//        AppAsset::class,
//        "yii\bootstrap\BootstrapAsset"
    ];

    public $jsOptions = [
        'position' => View::POS_END,
    ];
}
