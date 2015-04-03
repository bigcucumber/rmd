<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
        set_time_limit(0);
        ini_set("memory_limit","-1");

        if(false)
        {
            try
            {
                $sftp = Yii::app() -> sftp;
                $userinfo = new UserinfoSource($sftp);
                $userinfo -> download('userinfo_2015-04-01.csv','/cardletter/data/');
                $result = $userinfo -> readCsv();

                $userinfoDao = new UserinfoDao();
                $solrUtils = new SolrHelper($userinfoDao);
                $solrUtils -> solrSimpletools(CJSON::encode($result)); /* post 数据到solr */
            }
            catch(Exception $e)
            {
                $userinfo -> totalRow = -1;
                Yii::log($e -> getMessage(), "error");
            }
        }
        //if(false)
        {
            //echo CDateTimeParser::parse('2015/4/3  16:00:00',"yyyy/M/d  HH:mm:ss");exit;
            try
            {
                $sftp = Yii::app() -> sftp;
                $itemlist = new ItemlistSource($sftp);
                $itemlist -> download('itemlist_source_2015-04-01.csv','/cardletter/data/');
                $result = $itemlist -> readCsv();


                /* POST 数据到solr中 */
                $itemlistDao = new ItemlistDao();
                $solrUtils = new SolrHelper($itemlistDao);
                $solrUtils -> solrSimpletools(CJSON::encode($result)); /* post 数据到solr */

            }
            catch(Exception $e)
            {
                $itemlist -> totalRow = -1;
                Yii::log($e -> getMessage(), "error");
            }
        }

        if(false)
        {
            $sftp = Yii::app() -> sftp;
            $userlog = new UserlogSource($sftp);
            $userlog -> download('userlog_2015-04-01.csv','/cardletter/data/');
            $result = $userlog -> readCsv();
            echo '<pre>';
            print_r($result);


        }
    }


}
