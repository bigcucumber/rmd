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
     * csv总行数
     */
    public $totalRow = 0;


    /**
     * csv中有效数据
     */
    protected $_validData = array();

    /**
     * schema文件路径名称
     */
    protected $_schemaPath;


    /**
     * csv shcema 结构
     */
    protected $_schemaArray;


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
     * 设置 schema path 路径相对AppBasePath
     * @params string $path
     * @return null
     */
    public function setSchemaPath($path)
    {
        $this -> _schemaPath = $path;
    }

    /**
     * 获取schema path
     * @return string $path
     */
    public function getSchemaPath()
    {
        return $this -> _schemaPath;
    }

    /**
     * 设置有效数据
     * @param array $data
     * @return null
     */
    public function setValidData($data)
    {
        $this -> _validData = $data;
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
     * 获取有效数据
     * @return array $data
     */
    public function getValidData()
    {
        if(empty($this -> _validData))
        {
            return $this -> readCsv();
        }
        else
        {
            return $this -> _validData;
        }
    }


    /**
     * 读取csv数据,校验数据正确性
     * @param array $appendData 需要给每行添加数据段 array("title1" => '{title}') 将获取原始数据中的title字段
     * @return array $validData 校验好的数据
     */
    public function readCsv($appendData = array())
    {
        $csvpath = rtrim($this -> _csvstorepath, '/') . '/' . $this -> _csvname;
        $handle = fopen($csvpath,"rb+");
        $totalRow = -1;
        while($row = fgetcsv($handle))
        {
            $totalRow++;
            /* 跳过表头 */
            if(!isset($skipHead))
            {
                $skipHead = true;
                continue;
            }

            /* 验证数据 */
            $data = $this -> validate($row);

            if(!empty($data))
            {
                /* 需要添加字段 */
                if(!empty($appendData))
                {
                    foreach($appendData as $field => $value)
                    {
                        if(preg_match('/^\{.*\}$/',$value))
                        {
                            $index = rtrim(ltrim($value, '{'), '}');
                            if(isset($data[$index]))
                                $value = $data[$index];
                        }
                        $data[$field] = $value;
                    }
                }
                $this -> _validData[] = $data;
            }
        }
        fclose($handle);
        /* 记录总数 */
        $this -> totalRow = $totalRow;

        return $this -> _validData;
    }

    /**
     * 校验csv有效数据
     * @param array $data csv单行数据
     * @return array $row 是否验证成功
     */
    protected function validate($data)
    {
        if(empty($this -> _schemaArray))
        {
            $basePath = Yii::app() -> getBasePath();
            $schema = rtrim($basePath,'/') . '/' . $this -> _schemaPath;
            if(!is_file($schema))
                throw new Exception("data directory schemaUserinfo.php not exists!");
            $this -> _schemaArray = require_once($schema);
        }

        if(count($this -> _schemaArray) != count($data))
            return array();

        $index = -1;
        foreach($this -> _schemaArray as $field => $value)
        {
            $index++;
            /* 验证必要字段是否空 */
            if($value['isRequire'] && $data[$index] =='')
            {
                return array();
            }

            /* 数据转化 */
            switch($value['format'])
            {
                case "0":
                    break;
                case "date":
                    $formatString = (isset($value['formatString']) && $value['formatString'] != "") ? $value['formatString'] : "yyyy-dd-MM HH:ii";

                    $date = $data[$index];
                    if($pos = strpos($date,'.'))
                        $date = substr($date,0,$pos);

                    $timestamp = CDateTimeParser::parse($date,$formatString,array(
                        'year' => 1970,
                        'month' => 1,
                        'day' => 1
                    ));
                    $data[$index] = $timestamp ? $timestamp : 0;
                    break;
                case "trimspace":
                    $formatString = (isset($value['formatString']) && $value['formatString'] != "") ? $value['formatString'] : "_";
                    $data[$index] = preg_replace('/\s*/g',$data[$index], $formatString);
                    break;
                default:
                    break;
            }
        }

        /* 转化 array('username' => 'xxx','email' => 'xx@aa.com') */
        return array_combine(array_keys($this -> _schemaArray),$data);
    }

}
