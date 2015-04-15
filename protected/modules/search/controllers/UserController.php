<?php
/**
 * FileName: UserController.php
 * Description: 用户搜索借口
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-08 20:34:08
 * History:
 */
class UserController extends Controller
{
    /**
     * 根据用户email获取推送商品的id
     * @param string $id 用户email
     * @param integer $row 取出都上个商品
     * @param integer $currentDate 之前几天的
     * @param string $condition 额外条件,必须是符合solr查询语法 类似 title:xxxx AND price:[200 TO 200]
     * @return json $ids 商品id的json形式
     */
    public function actionId($id ="", $row = 1, $currentDate = 1, $like = '')
    {
        $userinfoDao = UserinfoDao::model() -> findByPk($id);
        if($userinfoDao == null || !array_key_exists('weight', $userinfoDao -> getAttributes())) /* 数据库中没有此用户*/
            $result = $this -> noUserinfo($row, $like);
        else
        {
            $weight = CJSON::decode($userinfoDao -> weight);
            $result = $this -> getIdsByWeight($row,$weight, $like);
            exit;
        }
        Yii::app() -> end(
            CJSON::encode($result)
        );
    }

    /**
     * 根据用户权重获取商品ids
     * @param integer $row 行数
     * @param array $weight 用户权重
     * @param string $like 附加条件
     */
    public function getIdsByWeight($row, $weight, $like)
    {
        uksort($weight,function($a,$b){
            if($a == $b)
                return 0;
            return ($a > $b) ? -1 : 1;
        });

        $sum = array_sum($weight);

        $likeArray = array();

        $criteria = new ASolrCriteria();
        foreach($weight as $key => $value)
        {
            $thisTypeCnt = ceil($row * ($value / $sum));
            $criteria -> query = "id:" . $key;
            $itemObj = AdOrderItemDao::model() -> find($criteria);
            if($itemObj == null)
            {
                $noArray = $this -> noUserinfo($thisTypeCnt);
                $likeArray = array_merge($likeArray, $noArray);
            }
            else
            {
                $itemIds = $itemObj -> item_ids;

                if(count($itemIds) > $thisTypeCnt)
                    $likeArray = array_merge($likeArray, array_slice($itemIds, 0, $thisTypeCnt));
                else
                {
                    $noArray = $this -> noUserinfo($thisTypeCnt - count($itemIds));
                    $likeArray = array_merge($likeArray,$itemIds);
                    $likeArray = array_merge($likeArray,$noArray);
                }

            }
        }

        return $likeArray;
    }

    /**
     * 当用户不存在数据库中,随机获取商品
     * @param integer $row  行数
     * @param string $like 附加条件
     * @return array $ids;
     */
    protected function noUserinfo($row, $like)
    {
        $query = "timestamp:[" . date('Ymd',strtotime('-1 day',time())) . ' TO ' . date('Ymd',time()) . ']';
        if($like != '')
            $query .= ' AND ( id:*'.$like.'* )';
        $criteria = new ASolrCriteria();
        $criteria -> query =  $query;
        $criteria -> setLimit($row);
        $adOrderItemObjs = AdOrderItemDao::model() -> findAll($criteria);

        $ids = array();
        foreach($adOrderItemObjs as $adOrderItemObj)
        {
            $ids = array_merge($adOrderItemObj -> item_ids,$ids);
            if(count($ids) >= $row)
                break;
        }
        return array_slice($ids, 0, $row);
    }


    /**
     * 随机获得商品id信息
     * @param integer $count 个数
     * @param json $json 随机的商品id信息
     */
    public function actionRandom($count)
    {
        $result = $this -> getRandom($count);
        echo CJSON::encode($result);
    }

    /**
     * 随机获取商品id
     * @parma integer $count 个数
     * @return array $ids
     */
    protected function getRandom($count)
    {
        $criteria = new ASolrCriteria();
        $criteria -> query = "*";

        $itemlistItem= ItemlistDao::model() -> find($criteria);
        if($itemlistItem == null)
            throw new Exception('the solr server is empty please import data in it');
        $allCnt = $itemlistItem -> getSolrResponse() -> getResults() -> total;
        if($allCnt < $count)
            $postion = 0;
        $postion = rand(0,$allCnt - $count);

        $criteria -> setOffset($postion);
        $criteria -> setLimit($count);

        $itemlistObjs = ItemlistDao::model() -> findAll($criteria);

        $ids = array();
        foreach($itemlistObjs as $item)
        {
            $ids[] = $item -> item_id;
        }
        unset($itemlistObjs);
        return $ids;
    }
}
