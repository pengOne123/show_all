<?php

/*5.原型模式 （Prototype）       start*/
namespace Prototype;

interface PrototypeInterface
{
    public function shallowCopy();

    public function deepCopy();
}

class Prototype implements PrototypeInterface
{
    private $_name;

    /**
     * Prototype constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * 浅复制
     */
    public function shallowCopy()
    {
        return clone $this;
    }

    /**
     * 深复制
     */
    public function deepCopy()
    {
        //序列化 变成字节流 相当于做了一次深复制 对引用的值也做了复制 引用的值将会变成不同的内存地址
        $serializeObj = serialize($this);
        $cloneObj     = unserialize($serializeObj);
        return $cloneObj;
    }

}

class Demo
{
    public $string;
}

class UserPrototype
{
    public function shallow()
    {
        $Demo               = new Demo();
        $Demo->string       = 'susan';
        $objectShallowFirst = new Prototype($Demo);
        $objectShallowSecond = $objectShallowFirst->shallowCopy();
        var_dump($objectShallowFirst->getName());
        echo '<br>';

        var_dump($objectShallowSecond->getName());
        echo '<br>';
        $Demo->string = 'sacha';
        var_dump($objectShallowFirst->getName());
        echo '<br>';

        var_dump($objectShallowSecond->getName());
        echo '<br>';
    }

    public function deep()
    {
        $Demo            = new Demo();
        $Demo->string    = 'Siri';

        //赋值
        $objectDeepFirst = new Prototype($Demo);

        //copy 深复制
        $objectDeepSecond = $objectDeepFirst->deepCopy();

        //获取最初的  Siri
        var_dump($objectDeepFirst->getName());
        echo '<br>';

        //深复制 Siri
        var_dump($objectDeepSecond->getName());
        echo '<br>';
        $Demo->string = 'Demo';

        //最初 被改变为 Demo
        var_dump($objectDeepFirst->getName());
        echo '<br>';

        //深复制则没有改变 Siri
        var_dump($objectDeepSecond->getName());
        echo '<br>';
    }
}

$UserPrototype = new UserPrototype();
$UserPrototype->shallow();
echo '<hr>';
$UserPrototype->deep();

//解决不共用一个实例 
//tips: 浅复制：复制了对象的变量  对引用的对象不做复制 被引用的还是原来的引用
//深复制：复制了对象的变量和引用 对引用的对象也做复制 被引用的改变不会影响当前的值