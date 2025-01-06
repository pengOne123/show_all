<?php
/*7.装饰器模式 （Decorator）       start*/

interface SourceAble
{
    public function method();
}

class Source implements SourceAble
{
    public function method()
    {
        echo 'The original method!';
    }
}

class Decorator implements SourceAble
{
    private $Source;

    /**
     * Decorator constructor.
     * @param $source
     */
    public function __construct($source)
    {
        $this->Source = $source;
    }

    //装饰方法
    public function method()
    {
        echo 'before decorator!';
        $this->Source->method();
        echo 'after decorator!';
    }
}

class DecoratorTest
{

    public static function main()
    {
        $Source = new Source();
        $obj    = new Decorator($Source);
        $obj->method();
    }

}

DecoratorTest::main();

//装饰器模式 主要是解决 动态扩展一个功能，还能撤销。缺点：产生很多装饰器,不容易排错. 可以统一管理装饰器减少排错时间,例如建立文件夹等。
