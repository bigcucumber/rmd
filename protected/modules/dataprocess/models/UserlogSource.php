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

        foreach($this -> _validData as $value)
        {
            $action = strtolower(trim($value['action']));
            foreach($allConfig['action']['behaviour'] as $actSet => $weight)
            {
                if($action == $actSet)
                {
                    $item_id = $value['item_id'];
                    if($item_id == '') /* 用户没有记录,随机赋值一条记录 */
                    {
                        if(!$item_id = ApplicationHelper::getState('random_item_id'))
                        {
                            $oCriteria = new ASolrCriteria();
                            $oCriteria -> query = "*";
                            $oCriteria -> setLimit(1);
                            $item_id = ItemlistDao::model() -> find($oCriteria) -> item_id;
                            ApplicationHelper::setState('random_item_id',$item_id,3600*14);
                        }
                    }

                    /* $result[trim($value['email'])]['action'][$action]数组不存在,或则item_id不存在数组中,添加 */
                    if(!isset($result[trim($value['email'])]['action'][$action]) || !in_array($item_id, $result[trim($value['email'])]['action'][$action]))
                        $result[trim($value['email'])]['action'][$action][] = $item_id;

                    /* 添加行为的时间 */
                    if(!isset($result[trim($value['email'])]['timelog'][$action]) || !in_array($value['timestamp'], $result[trim($value['email'])]['timelog'][$action]))
                    $result[trim($value['email'])]['timelog'][$action][] = $value['timestamp'];

                    /* 添加分类信息 */
                    $type = ($value['type'] == '') ? 'null' : preg_replace('/\s/', '_',strtolower($value['type'])); /* 得到用户分类,没有分类为null */
                    if(!isset($result[trim($value['email'])]['tag'][$type]))
                        $result[trim($value['email'])]['tag'][$type] = 0;
                    $result[trim($value['email'])]['tag'][$type] += $weight;

                }

            }
        }
        return $this -> _tidyData = $result;
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
            foreach($value as $k => $v)
            {
                if($k == 'tag')
                {
                    continue;
                }
                foreach($v as $n => $m)
                {
                    $temp[$n.'_'.$k] = $m;
                }
            }
            $result[] = array_merge($temp,array('email' => $email));
        }
        echo '<pre>';
        print_r($result);
        exit;
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
