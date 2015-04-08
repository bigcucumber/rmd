<?php
/**
 * FileName: SolrHelper.php
 * Description: solr的操作类
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-03 11:08:15
 * History:
 */
class SolrHelper
{

    /**
     * solr Dao Object
     */
    protected $_instance;

    /**
     * 构造方法
     * @param 对应的solr实例
     * @return null;
     */
    public function __construct($instance)
    {
        $this -> _instance = $instance;
    }

    /**
     * 获取solrHelper需要推送的实例
     * @reutrn object $instance
     */
    public function getInstance()
    {
        return $this -> _instance;
    }

    /**
     * 设置solrHelper需要推送的实例
     * @param object 对应的solrDao模型
     * @return null
     */
    public function setInstance($instance)
    {
        $this -> _instance = $instance;
    }

    /**
     * 仿command line simple post tool
     * @param string $contents 需要post的数据
     * @param boolean $commit 是否提交
     * @param string mime-type 类型
     * @return null
     * @throw Exception
     */
    public function solrSimpletools($contents, $commit = true, $wt = "json")
    {
        //echo $contents;exit;
        /* solr client configure */
        $instanceConfig = $this -> _instance -> getSolrConnection() -> clientOptions;
        $solrHost = rtrim(Yii::app() -> params['solrConfig']['host']);
        $url = $solrHost . ':' . $instanceConfig['port'] . rtrim($instanceConfig['path'],'/') . '/update?commit=' . $commit . '&wt=' . $wt;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contents);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/'.$wt, 'Content-Length: ' . strlen($contents))); 
        $result = curl_exec($ch);

        $arrRes = CJSON::decode($result);
        if($arrRes['responseHeader']['status'] != 0)
            throw new Exception(json_encode($arrRes));
    }
}

