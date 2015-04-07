#rmd(Recommend System base userinfo.csv and userlog.csv)
获取网站用户信息,记录用户浏览,购买,收藏,加入购物车的商品记录,计算出用户对商品的权重信息,计算后根据用户权重,自定一的推荐商品.

##2015-04-01
    * CSVSource.php csv数据基类
    * UserinfoSource.php userinfo.csv类
```php
    $sftp = Yii::app() -> sftp;
    $userinfo = new UserinfoSource($sftp); // 得到userinfo.csv对象
    $userinfo -> download('userinfo_2015-04-01.csv','/cardletter/data/'); // 下载
    $result = $userinfo -> readCsv(array('haah' => '{email}')); // 获取数据 校验数据信息在/data/schemaUserinfo.php中
    echo '<pre>';
    print_r($result);
```
##2015-04-2
    * ItemlistSource.php itemlist_source.csv类
    * solr schema design

##2015-04-07
    * solr配置,solr中的uniqeKey不能是int类型
    * 可以将userinfo.csv,itemlist_source.csv数据导入到solr服务器中
