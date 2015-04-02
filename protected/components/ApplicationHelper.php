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

}
