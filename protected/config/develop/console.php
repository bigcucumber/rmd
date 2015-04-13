<?php
/**
 * FileName: main.php
 * Description: 所有配置分开入口文件
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 10:36:00
 * History:
 */
$consoleConfig = CMap::mergeArray(
    require(dirname(__FILE__).'/require.php'),
    require(dirname(__FILE__).'/module.php'),
    require(dirname(__FILE__).'/components.php')
);

//unset($consoleConfig['']);
return $consoleConfig;
