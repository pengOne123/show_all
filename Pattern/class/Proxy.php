<?php
/*8.代理模式 （Proxy）       start*/

interface SourceAbleInterface
{
    public function method();
}

class Source implements SourceAbleInterface
{
    public function method()
    {
        echo 'The original method'.'<br>';
    }
}

class Proxy implements SourceAbleInterface
{
    private $Source;

    public function __construct()
    {
        $this->Source = new Source();
    }

    public function method()
    {
        $this->after();
        $this->Source->method();
        $this->before();
    }

    public function after()
    {
        echo 'after proxy'.'<br>';
    }

    public function before()
    {
        echo 'before proxy'.'<br>';
    }

}

class ProxyTest
{
    public static function main()
    {
        $Source = new Proxy();
        $Source->method();
    }

}

ProxyTest::main();
//应用场景：已有的方法进行改进  1.修改原有方法，这样违反了开闭原则 2.使用代理模式
//使用代理模式，可以将功能划分的更加清晰，有利于后期维护。
