<?php
/**
 * FileName: params.php
 * Description: Yiiframework Params Configure File
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 10:31:21
 * History:
 */
return array(
    'basePath'=>dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'..',
    'name'=>'RMD',
    'preload'=>array('log'),
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.extensions.sftp.*',
        'application.extensions.YiiSolr.*',
    ),

    'params'=>array(
        'adminEmail'=>'webmaster@example.com',

        'solrConfig' => array(
            'host' => 'http://192.168.1.108',
            'port' => '8983',
        ),
    ),

);
