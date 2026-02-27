<?php

namespace app\assets;

use yii\web\AssetBundle;

class RegisterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/register.css',
    ];

    public $js = [
        'js/register.js',
    ];

    public $depends = [
        'hail812\adminlte3\assets\AdminLteAsset',
    ];
}
