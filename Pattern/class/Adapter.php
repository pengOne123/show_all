<?php

/*6.适配器模式 （Adapter）       start*/

/*01.类的适配器模式*/

class Source
{
    public function method1()
    {
        echo 'this is original method!';
    }

}

interface TargetTableInterface
{
    /*与原类方法相同*/
    public function method1();

    /*新类的方法*/
    public function method2();

}

class Adapter extends Source implements TargetTableInterface
{
    public function method2()
    {
        echo 'this is TargetTable!';
    }
}

class AdapterTest
{
    public static function main()
    {
        $Adapter = new Adapter();
        $Adapter->method1();
        $Adapter->method2();
    }
}

//AdapterTest::main();

/*end*/

/*02.对象的适配器模式*/
//
//class Wrapper implements TargetTableInterface
//{
//    private $source;
//
//
//    /**
//     * Wrapper constructor.
//     * @param $source
//     */
//    public function __construct($source)
//    {
//        $this->source = $source;
//    }
//
//
//    public function method1()
//    {
//        $this->source->method1();
//    }
//
//    public function method2()
//    {
//        echo 'this is TargetTable2!';
//    }
//
//}
//
//class AdapterTest2
//{
//    public static function main()
//    {
//        $Source = new Source();
//        $Target = new Wrapper($Source);
//        $Target->method1();
//        $Target->method2();
//    }
//}
//
//AdapterTest2::main();

/*end*/

/*03.接口的适配器模式*/

interface SourceTableInterface
{

    public function method1();

    public function method2();

}

abstract class Wrapper2 implements SourceTableInterface
{
    public function method1()
    {
    }

    public function method2()
    {
    }
}

class SourceSub1 extends Wrapper2
{
    public function method1()
    {
        echo 'this is SourceSub1!';
    }
}

class SourceSub2 extends Wrapper2
{
    public function method2()
    {
        echo 'this is SourceSub2!';
    }
}


class WrapperTest
{
    public static function main()
    {
        $Source1 = new SourceSub1();
        $Source2 = new SourceSub2();

        $Source1->method1();
        $Source1->method2();
        $Source2->method1();
        $Source2->method2();
    }
}

WrapperTest::main();
//目的是消除接口不匹配造成的兼容性问题
//不同：01是method2新类继承method1类 只实现method2即可 02是method2新类实现method2 实现method1的时候直接调用method1类 03开始有一个抽象类 里面有method1 method2   method1 method2 是不同的两个类 method1 method2 继承抽象类 method1类实现method1 method2类实现method2

