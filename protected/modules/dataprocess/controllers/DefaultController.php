<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
        set_time_limit(0);
        ini_set("memory_limit","-1");

        try
        {
            $sftp = Yii::app() -> sftp;
            $userinfo = new UserinfoSource($sftp);
            $userinfo -> download('userinfo_2015-04-01.csv','/cardletter/data/');
            $result = $userinfo -> readCsv(array('haah' => '{email}'));
            echo '<pre>';
            print_r($result);
        }
        catch(Exception $e)
        {
            $userinfo -> totalRow = -1;
            Yii::log($e -> getMessage(), "error");
        }
	}
}
