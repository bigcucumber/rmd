<?php
/**
 * FileName: UserinfoSource.php
 * Description: 用户信息csv资源
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-02 12:01:14
 * History:
 */
class UserinfoSource extends CSVSource
{

    /**
     * csv总行数
     */
    public $totalRow = 0;

    /**
     * csv中有效数据
     */
    private $_validData = array();


    /**
     * csv shcema 结构
     */
    private $_schemaArray;

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
            $schema = rtrim($basePath,'/') . '/data/schemaUserinfo.php';
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

                    $data[$index] = CDateTimeParser::parse($date,$formatString);
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
