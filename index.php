<?php

$yii = dirname(__FILE__).'/../framework1.1.14/yii.php';

$env = "develop";
$config=dirname(__FILE__).'/protected/config/' . $env . '/main.php';

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
