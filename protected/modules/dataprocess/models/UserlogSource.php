<?php
/**
 * FileName: UserlogSource.php
 * Description: 用户行为资源数据
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-03 10:46:03
 * History:
 */
class UserlogSource extends CSVSource
{
    /**
     * userinfo schema config path
     */
    protected $_schemaPath = "data/schemaUserlog.php";

    /**
     * 整理好的数据
     */
    protected $_tidyData = null;


    /**
     * 根据用户条件合并成的tag分割符号
     */
    const TAG_SEPARATE = "#";

    /**
     * 获取整理好的数据
     */
    public function getTidyData()
    {
        return $this -> _tidyData;
    }

    /**
     * 根据用数据,整理出
     * array(
     *   'user@demo.com' => array(
     *       'action' => array(
     *           'view' => array(
     *               'item_ids',
     *               ....
     *           ),
     *           'buy' => array(
     *               'item_ids',
     *               ...
     *           ),
     *           'store' => array(
     *               'item_ids',
     *               ...
     *           ),
     *           'cart' => array(
     *               'item_ids',
     *               ...
     *           )
     *       ),
     *       'tag' => array(
     *           'shoes' => 2,
     *           'dress' => 3,
     *           ...
     *       ),
     *       'time' => array(
     *           'view' => array(
     *               'timestamp',
     *               ....
     *           ),
     *           'buy' => array(
     *               'timestamp',
     *               ...
     *           ),
     *           'store' => array(
     *               'timestamp',
     *               ...
     *           ),
     *           'cart' => array(
     *               'timestamp',
     *               ...
     *           )
     *       )
     *    )
     * )
     * @reutrn array $result 同上数组
     */
    public function tidyUserlog()
    {
        if(empty($this -> _validData))
            throw new Exception('please download userlog.csv and validate it');

        if($this -> _tidyData != null)
            return $this -> _tidyData;

        $result = array();
        $allConfig = $this -> getSchemaConfig(); /* array('index' => 6, 'behaviour' => array('biew','buy'....)) */

        $itemlistDao = ItemlistDao::model();
        foreach($this -> _validData as $value)
        {
            $action = strtolower(trim($value['action']));
            foreach($allConfig['action']['behaviour'] as $actSet => $weight)
            {
                if($action == $actSet)
                {
                    $item_id = ($value['item_id'] == '') ? 0 : $value['item_id'];
                    $itemObj = $itemlistDao -> findByPk($item_id);
                    if($itemObj == null) /* 用户没有记录,随机赋值一条记录 */
                    {
                        if(!$itemObj = ApplicationHelper::getState('random_item_obj'))
                        {
                            $oCriteria = new ASolrCriteria();
                            $oCriteria -> query = "*";
                            $oCriteria -> setLimit(1);
                            $itemObj = $itemlistDao -> find($oCriteria);
                            ApplicationHelper::setState('random_item_obj',$itemObj,3600*14);
                        }
                    }

                    $item_id = $itemObj -> item_id;

                    /* $result[trim($value['email'])]['action'][$action]数组不存在,或则item_id不存在数组中,添加 */
                    if(!isset($result[trim($value['email'])]['action'][$action]) || !in_array($item_id, $result[trim($value['email'])]['action'][$action]))
                        $result[trim($value['email'])]['action'][$action][] = $item_id;

                    /* 添加行为的时间 */
                    if(!isset($result[trim($value['email'])]['timelog'][$action]) || !in_array($value['timestamp'], $result[trim($value['email'])]['timelog'][$action]))
                        $result[trim($value['email'])]['timelog'][$action][] = $value['timestamp'];

                    /* 添加分类信息 */
                   #$type = $value['type']; /* 用户行为中没有type字段,根据item_id去itemlist中获取type */
                   #if($type == '')
                   #    $type = ($itemObj -> type == '') ? 'null' : $itemObj -> type;
                   #$type = preg_replace('/\s/', '_',strtolower($value['type'])); /* 得到用户分类,没有分类为null */
                    $type = $this -> itemFilter($itemObj);

                    if(!isset($result[trim($value['email'])]['tag'][$type]))
                        $result[trim($value['email'])]['tag'][$type] = 0;
                    $result[trim($value['email'])]['tag'][$type] += $weight;

                }

            }
        }

        return $this -> _tidyData = $result;
    }

    /**
     * 根据用户自定义条件分类
     * @param array $item 单行csv记录的数组
     * @return array $result
     */
    protected function itemFilter($item)
    {
        $itemlistCategoryConfig = Yii::app() -> params['itemlistCategory'];
        $conditionKey = '';
        foreach($itemlistCategoryConfig['fields'] as $key => $value)
        {
            // 取出该商品的类别值做为分类的键名
            if(isset($value['mapper']) && isset($value['conditions']))
            {
                $conditionKey .= call_user_func(
                    array($this,$value['mapper']),
                    array('conditions' => $value['conditions'],'value' => $item[$key])
                ) . self::TAG_SEPARATE;
            }
            else
            {
                //if(empty($item[$key])) continue; // 不存在的字段,需要干掉
                $item[$key] = ($item[$key] == '') ? 'null' : $item[$key];
                if(isset($value['mapper'])) // 有对该数据字段处理的
                    $conditionKey .= call_user_func(
                        array($this, $value['mapper']),
                        $key,
                        $item
                    ) . self::TAG_SEPARATE;
                else
                    $conditionKey .= $item[$key] . self::TAG_SEPARATE;
            }
        }

        return trim($conditionKey,self::TAG_SEPARATE);

    }

    /**
     * 构建更新userinfo信息数组
     * @return json $data 更新用户信息的json字串
     */
    public function updateUserinfoSource()
    {
        $data = $this -> tidyUserlog();
        $result = array();
        foreach($data as $email => $value)
        {
            $temp = array();
            $userinfo = array('email' => $email);
            foreach($value as $k => $v)
            {
                if($k == 'tag')
                {
                    $userinfo['weight'] = $this -> getWeight($email, $v);
                    continue;
                }
                foreach($v as $n => $m)
                {
                    $temp[$n.'_'.$k] = $m;
                }
            }
            $result[] = array_merge($temp,$userinfo);
        }
        return $result;
    }

    /**
     * 获取用户权限,判断用户是否已存在
     * @param array $tag tag信息
     * @param string $email 用户邮箱
     * @return json 用户权重json信息
     */
    public function getWeight($email,$tag)
    {

        $originWeight = $tag;

        $userinfoDao = new UserinfoDao();
        $userinfoDaoObj = $userinfoDao -> findByPk($email);

        $olderWeight = array();
        if($userinfoDaoObj != null && property_exists($userinfoDaoObj, 'weight'))
            $olderWeight = CJSON::decode($userinfoDaoObj -> weight);

        return $weight = CJSON::encode(
            WeightHelper::getNewWeight($olderWeight, $originWeight)
        );
    }


    /**
     * 获取用户配置信息
     */
    protected function getSchemaConfig()
    {
        $basePath = Yii::app() -> getBasePath();
        $allConfig = require($basePath . '/data/schemaUserlog.php');
        return $allConfig;
    }
}
