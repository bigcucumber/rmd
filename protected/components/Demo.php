<?php
class Demo
{
    protected $_name = "luowen";
    protected $_age = 23;
    protected $_obj;

    public function __construct($obj)
    {
        $this -> _obj = $obj;
    }

    public function setName($name)
    {
        $this -> _name = $name;
    }
    public function setAge($age)
    {
        $this -> _age = $age;
    }

    public function getName()
    {
        return $this -> _name;
    }

    public function getAge()
    {
        return $this -> _age;
    }
}
