<?php
/**
 * FileName: CSVSource.php
 * Description: 用户提供的csv数据源
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 11:39:25
 * History:
 */

abstract class CSVSource
{
    /**
     * sftp 对象
     */
    private $_sftp;

    /**
     * 下载csv文件默认存放路径
     */
    protected $_csvstorepath;

    /**
     * csv名称
     */
    protected $_csvname;

    /**
     * @param Object sftp对象信息
     */
    public function __construct($sftp)
    {
        /* 默认存放路径 */
        $this -> _csvstorepath = Yii::app() -> getBasePath() . '/data/sftp/csvsource/';
        $this -> _sftp = $sftp;
    }

    /**
     * 设置csv存放路径
     * @param string $path 
     * @return void
     */
    public function setCsvstorepath($path)
    {
        $this -> _csvstorepath = $path;
    }

    /**
     * 获取csv存放路径
     * @return string $path
     */
    public function getCsvstorepath()
    {
        return $this -> _csvstorepath;
    }

    /**
     * 下载方法
     * @param string $serverPath 目标文件路径,相对宿主目录
     * @param string $localPath 保存本地path
     * @param string $filename 文件名字
     * @param strign $localFilename 本地文件存放名字
     * @param boolean $redownload 文件存在时候,是否更新下载
     * @return void
     * @throw Exception
     */
    public function download($filename, $serverPath = "/", $localPath = "", $localFilename = "", $redownload = false)
    {
        /* 给下载的csv名称赋值 */
        if($localFilename == '')
           $this -> _csvname =  $filename;
        else
            $this -> _csvname = $localFilename;

        /* 存储路径 */
        if($localPath == "")
            $localPath = $this -> _csvstorepath;
        else
            $this -> _csvstorepath = $localPath;

        if(!is_dir($localPath))
            ApplicationHelper::mkdir($localPath);

        $localFilenameAbPath = rtrim($localPath,'/') . '/' . (($localFilename == "") ? $filename : $localFilename);
        /* 文件下载完成 */
        if(is_file($localFilenameAbPath) && !$redownload) return;

        $this -> _sftp -> connect();
        $currentDir = $this -> _sftp -> getCurrentDir();

        /* $relationPath = cardletter/data 获取这种类型的path 拼接 */
        if($serverPath == "" || $serverPath == "/")
            $relationPath = "";
        else
            $relationPath = $serverPath;

        $serverStore = rtrim($currentDir, '/') . '/' . trim($relationPath , '/') . '/' . $filename;
        $localStore = rtrim($localPath, '/') . '/' . $this -> _csvname;

        $this -> _sftp -> getFile($serverStore, $localStore);
    }

    /**
     * 验证csv信息
     */
    protected function _validate(){}
}
