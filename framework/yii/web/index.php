<?php

// require '../../../cesp/cesp_log.php';
// cesp_log('start');

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();


// cesp_log('end');
// echo '<pre>';
// cesp_log('print');
// echo '</pre>';
