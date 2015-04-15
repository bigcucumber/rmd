<?php
/**
 * FileName: WeightHelper.php
 * Description: 计算用户权重帮助类
 * Author: Bigpao
 * Email: bigpao@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2014-11-24 11:01:24
 * History:
 */

class WeightHelper
{


    /**
     * 重新计算用户的权重
     * @param array $olderWeight 用户旧的权重
     * @pram array $newWeight 用户新的权重
     * @return array 新的权重
     */
    public static function getNewWeight($olderWeight,$newWeight,$alpha = '')
    {
        //计算后的权重存放容器
        $result = array();

        // 获取权重系数
        $alpha = Yii::app() -> params['alpha'];

        // 存放所有新商品,就商品的key
        $keys = array_merge(
            array_keys($olderWeight),
            array_keys($newWeight)
        );

        if(!empty($newWeight))
        {
            $newWeight = self::getUnitaryWeight($newWeight);
        }

        foreach($keys as $key)
        {
            $older = isset($olderWeight[$key]) ? $olderWeight[$key] : 0;
            $newer = isset($newWeight[$key]) ? $newWeight[$key] : 0;

            //计算新的权重
            $result[$key] = ( 1 - $alpha) * $older + $alpha * $newer;
        }

        return self::getUnitaryWeight($result);
    }



    // 对商品进行排序
    public static function getUnitaryWeight($items)
    {
        if(!empty($items))
        {
            $total = 0;

            // 求出tiems中所有的权限和
            foreach($items as $key => $value)
            {
                $total += $value;
            }

            // 求出items中每个商品的权重占总权重的比例
            foreach($items as $key => $value)
            {
                $items[$key] = number_format($value / $total,5);
                //$items[$key] = $value / $total;
            }

            // 商品超过20个的时候,对商品排序后在取出前20个比例
            $size = 20;
            if(count($items) > $size)
            {
                uasort($items,array('WeightHelper','sortByValue'));

                // 截取前20个值
                $temp = array();
                $i = 0;
                foreach($items as $key => $value)
                {
                    if($i < 20)
                    {
                        $temp[$key] = $value;
                        $i++;
                    }
                    else
                        break;
                }

                //print_r($items);
                //exit;
                $items = $temp;
            }

            return $items;
        }
    }

    //自定一排序
    public static function sortByValue($a,$b)
    {
        if($a == $b)
            return 0;
        return $a > $b ? -1 : 1;
    }

}
