<?php

class DefaultController extends Controller
{
    public function actionIndex()
    {
        $criteria = new ASolrCriteria();
        $criteria -> query = "*";
        $userinfoDao = UserinfoDao::model() -> find($criteria);
        var_dump($userinfoDao);
        exit;
    }
}
