# rmd
    Recommend System base userinfo.csv and userlog.csv

    2015-04-01
        * CSVSource.php csv数据基类
        * UserinfoSource.php userinfo.csv类

<code>
    $sftp = Yii::app() -> sftp;
    $userinfo = new UserinfoSource($sftp); // 得到userinfo.csv对象
    $userinfo -> download('userinfo_2015-04-01.csv','/cardletter/data/'); // 下载
    $result = $userinfo -> readCsv(array('haah' => '{email}')); // 获取数据 校验数据信息在/data/schemaUserinfo.php中
    echo '<pre>';
    print_r($result);
</code>
