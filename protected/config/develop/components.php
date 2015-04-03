<?php
/**
 * FileName: components.php
 * Description: Yiiframework Components Config
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 10:28:51
 * History:
 */

/* 获取用户配置的sftpComponents配置信息 */
function getSftpComponent()
{

    $sftpComponents = array(
        'class' => 'application.extensions.sftp.SftpComponent',
        'host'=>'sftp.idc.webpowerchina.cn',
        'port'=>22,
        'username'=>'dma',
        'password'=>'.U5x2OFjBwc7v',
    );

    // 用户设置了sftp配置,产生一个文件
    if(file_exists(dirname(__FILE__) . '/../data/sftpConnConfig.php'))
        $sftpConnConfig = require(dirname(__FILE__) . '/../data/sftpConnConfig.php');

    // 合并用户配置的conn信息
    $sftpComponents = isset($sftpConnConfig) ? array_merge($sftpComponents,$sftpConnConfig) : $sftpComponents;
    return $sftpComponents;
}

return array(
    // application components
    'components'=>array(
        'user'=>array(
            // enable cookie-based authentication
            'allowAutoLogin'=>true,
        ),


        // uncomment the following to enable URLs in path-format
        'urlManager'=>array(
            'urlFormat'=>'path',
            'urlSuffix' => '.html',
            /* 'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ), */
        ),

        'db'=>array(
            'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
        ),
        // uncomment the following to use a MySQL database
        /*
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=testdrive',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),
         */
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                    'logFile' => 'app.'.date("Y-m-d",time()).'.log'
                ),
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
                 */
            ),
        ),

        // sftp component
        'sftp' => getSftpComponent(),

        /* solr instance configure segment */
        'solrUserinfo' => array(
            'class' => 'application.extensions.YiiSolr.ASolrConnection',
            'clientOptions' => array(
                'hostname' => '192.168.1.108',
                'port' => '8983',
                'path' => '/solr/UserinfoSource',
            ),
        ),
        'solrItemlist' => array(
            'class' => 'application.extensions.YiiSolr.ASolrConnection',
            'clientOptions' => array(
                'hostname' => '192.168.1.108',
                'port' => '8983',
                'path' => '/solr/ItemlistSource',
            ),
        ),


    ),


);
