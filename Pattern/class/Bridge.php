<?php
/*10.桥接模式 （Bridge）      start*/
namespace Bridge;

interface SourceAbleInterface
{
    public function method();
}

class SourceSub1 implements SourceAbleInterface
{
    public function method()
    {
        echo 'this is the first sub!' . '<br>';
    }

}

class SourceSub2 implements SourceAbleInterface
{
    public function method()
    {
        echo 'this is the second sub!' . '<br>';
    }

}

//抽象类 里面的类必定存在
abstract class Bridge
{
    private $Source;

    public function method()
    {
        $this->Source->method();
    }

    public function getSource()
    {
        return $this->Source;
    }

    public function setSource(SourceAbleInterface $source)
    {
        $this->Source = $source;
    }
}

class MyBridge extends Bridge
{
    public function method()
    {
        $this->getSource()->method();
    }
}

class BridgeTest
{
    public static function main()
    {
        $MyBridge = new MyBridge();
        /*调用第一个对象*/
        $SourceSub1 = new SourceSub1();
        $MyBridge->setSource($SourceSub1);
        $MyBridge->method();
        /*调用第二个对象*/
        $SourceSub2 = new SourceSub2();
        $MyBridge->setSource($SourceSub2);
        $MyBridge->method();
    }

}

BridgeTest::main();

//主要是把事务和其具体实现分开，使他们可以独立的变化。桥接的用意是：将抽象化和实现化解耦，使得二者可以独立变化。
//BridgeTest 可以变成工厂类 SourceSub 当做数据库外部接口 MyBridge统一调用。
