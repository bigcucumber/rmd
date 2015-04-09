<?php

class DefaultController extends Controller
{
    public function actionStart()
    {
        set_time_limit(0);
        ini_set("memory_limit","-1");

        /* 获取sftp配置信息 */
        $sftpDataConfig = $this -> getftpDataConfig();
        $relativePath = '/' . trim($sftpDataConfig['dataStorePath'], '/') . '/';
        $nowTime = date("Y-m-d",time());
        $nowTime = "2015-04-01";

        $userinfoSource = str_replace('date', $nowTime, $sftpDataConfig['userinfoSource']);
        $userlogSource = str_replace('date', $nowTime, $sftpDataConfig['userlogSource']);
        $itemlistSource = str_replace('date', $nowTime, $sftpDataConfig['itemlistSource']);


        $solrUtils = new SolrHelper();
        if(false)
        {
            try
            {
                $sftp = Yii::app() -> sftp;
                $userinfo = new UserinfoSource($sftp);
                $userinfo -> download($userinfoSource, $relativePath);
                $result = $userinfo -> readCsv();

                $userinfoDao = new UserinfoDao();
                $solrUtils -> setInstance($userinfoDao);
                $solrUtils -> solrSimpletools(CJSON::encode($result)); /* post 数据到solr */
            }
            catch(Exception $e)
            {
                $userinfo -> totalRow = -1;
                Yii::log($e -> getMessage(), "error");
            }
        }
        if(false)
        {
            //echo CDateTimeParser::parse('2015/4/3  16:00:00',"yyyy/M/d  HH:mm:ss");exit;
            try
            {
                $sftp = Yii::app() -> sftp;
                $itemlist = new ItemlistSource($sftp);
                $itemlist -> download($itemlistSource, $relativePath);
                $result = $itemlist -> readCsv();

                /* POST 数据到solr中 */
                $itemlistDao = new ItemlistDao();
                $solrUtils -> setInstance($itemlistDao);
                $solrUtils -> solrSimpletools(CJSON::encode($result)); /* post 数据到solr */

                $adOrderItemArr = $itemlist -> category();
                $adOrderItemDao = new AdOrderItemDao();
                $solrUtils -> setInstance($adOrderItemDao);
                $solrUtils -> solrSimpletools(CJSON::encode($adOrderItemArr)); /* post adOrderItem数据到solr */

            }
            catch(Exception $e)
            {
                $itemlist -> totalRow = -1;
                Yii::log($e -> getMessage(), "error");
            }
        }

        //if(false)
        {
            try
            {
                $sftp = Yii::app() -> sftp;
                $userlog = new UserlogSource($sftp);
                $userlog -> download($userlogSource, $relativePath);
                $userlog -> readCsv();

                echo '<pre>';print_r($userlog -> tidyUserlog());exit;

                $result = $userlog -> updateUserinfoSource();

                $userinfoDao = new UserinfoDao();
                $solrUtils -> setInstance($userinfoDao);
                $solrUtils -> solrSimpletools(CJSON::encode($result)); // 更新用户权重信息
            }
            catch(Exception $e)
            {
                throw $e;
                $userlog -> totalRow = -1;
                Yii::log($e -> getMessage(), 'error');
            }

        }


        /* 记录日志中 */
        if(false)
        {
            $statisticDao = new StatisticDao();
            $id = date("Ymd",strtotime("-1 day",time()));
            $statisticDao -> id = $id;
            $statisticDao -> userinfo_cnt = $userinfo -> totalRow;
            $statisticDao -> userlog_cnt = $userlog -> totalRow;
            $statisticDao -> itemlist_cnt = $itemlist -> totalRow;

            $criteria = new ASolrCriteria();
            $criteria -> query = "id:".$id;
            $todayLoged = StatisticDao::model() -> find($criteria);
            if($todayLoged != null) // 今天同步了,不累加
            {
                $userinfo -> userinfo_cnt = 0;
                $sumUserinfoCnt = $todayLoged -> sum_userinfo_cnt;
            }
            else // 今天没有同步,累加昨天的记录
            {
                $criteria -> query = "*";
                $criteria -> setOrder('id desc');
                $sumCntObj = StatisticDao::model() -> find($criteria);
                $sumUserinfoCnt = ($sumCntObj == null) ? 0 : $sumCntObj -> sum_userinfo_cnt; // 记录总数
            }

            $todayUserinfoCnt = ($userinfo -> totalRow >= 0) ? $statisticDao -> userinfo_cnt : 0; // 记录今天数
            $statisticDao -> sum_userinfo_cnt = $sumUserinfoCnt + $todayUserinfoCnt;

            if(!$statisticDao -> save())
                Yii::log(json::encode($statisticDao -> getErrors()),'error');

        }

        Yii::app() -> end("ok");

    }

    /**
     * 获取用户配置信息
     * @return array $config
     */
    protected function getftpDataConfig()
    {
        $basePath = Yii::app() -> getBasePath();
        $configPath = $basePath . '/data/sftpDataConfig.php';
        if(!file_exists($configPath))
            throw new Exception('the sftp configure file is not under data directory check it');
        return require($configPath);
    }

    public function actionIndex()
    {
        $res = Yii::app() -> getComponent('solrItemlist');
        var_dump($res);
        exit;
        $result = $this -> getftpDataConfig();
        echo '<pre>'; print_r($result);exit;
    }

}
