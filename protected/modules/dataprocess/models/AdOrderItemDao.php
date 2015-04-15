<?php
/**
 * FileName: AdOrderItemDao.php
 * Description: adOrderItem 广告商品订单项
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-08 11:00:57
 * History:
 */
class AdOrderItemDao extends ASolrDocument
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
        return Yii::app() -> solrAdOrderItem;
    }

}
