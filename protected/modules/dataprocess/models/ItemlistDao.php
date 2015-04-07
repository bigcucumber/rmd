<?php
/**
 * FileName: ItemlistDao.php
 * Description: itemlist source file
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-03 21:15:29
 * History:
 */
class ItemlistDao extends ASolrDocument
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
        return Yii::app() -> solrItemlist;
    }

    public function primaryKey()
    {
        return 'item_id';
    }

}
