<?php
/**
 * FileName: ApplicationHelper.php
 * Description: 应用帮助类
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 11:45:22
 * History:
 */

class ApplicationHelper
{
    /**
     * 递归穿件多个目录
     * @pram string $dir 需要穿件的目录 d:/xx/cc/dd/ee/ff/dd/
     */
    public static function mkdir($dir)
    {
        if(!is_dir(dirname($dir)))
        {
            self::mkdir(dirname($dir));
        }
        @mkdir($dir);
    }

    /**
     * 获取缓存值
     * @param string $key 缓存的键
     * @return mixed 存储的值,没有为null
     */
    public static function getState($key)
    {
        return Yii::app() -> cache -> get($key);
    }

    /**
     * 缓存方法
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int $expire 缓存时间
     * @param int $dependency 缓存依赖
     * @return boolean $flag 成功否
     */
    public static function setState($key,$value,$expire = 0, $dependency=NULL)
    {
        return Yii::app() -> cache -> set($key,$value,$expire,$dependency);
    }

    /**
     * 删除缓存的值
     * @param string $key 缓存的贱名字
     * @return boolean 首付成功
     */
    public static function deleteState($key)
    {
        return Yii::app() -> cache -> delete($key);
    }
}
