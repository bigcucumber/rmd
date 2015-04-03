<?php
/**
 * FileName: UserinfoDao.php
 * Description: userinfo solr 数据访问层
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-03 16:43:48
 * History:
 */
class UserinfoDao extends ASolrDocument
{
     /**
     * Required for all ASolrDocument sub classes
     * @see ASolrDocument::model()
     */
    public static function model($className = __CLASS__) 
    {
        return parent::model($className);
    }
    /**
     * @return ASolrConnection the solr connection to use for this model
     */
    public function getSolrConnection() 
    {
        return Yii::app() -> solrUserinfo;
    }

    public function primaryKey()
    {
        return 'email';
    }

}
