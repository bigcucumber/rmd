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
        'application.modules.dataprocess.models.*',
        'application.extensions.sftp.*',
        'application.extensions.YiiSolr.*',
    ),

    'params'=>array(
        'adminEmail'=>'webmaster@example.com',

        'solrConfig' => array(
            //'host' => 'http://192.168.3.143',
            'host' => 'http://192.168.3.143',
            'port' => '8983',
        ),

        /* 商品分类纬度 */
        'itemlistCategory' => array(
            'fields' => array(
                'type' => array(
                    'mapper' => 'trimSpace'
                ),
                'price3' => array(
                    'mapper' => 'priceRange', // 处理方法
                    'conditions' => array( // 处理条件
                        'segment1' => 80,
                        'segment2' => 200,
                        'segment3' => 500,
                        'segment4' => 1000,
                        'segment5' => 1000000000000000000
                    ),
                ),
            ),
        ),

        'alpha' => 0.2, /* 用户权重系数 */
    ),

);
