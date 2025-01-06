<?php

/*17.责任链模式 （Chain of Responsibility）      start*/

interface HandlerInterface
{
    public function operator();
}

abstract class HandlerAbstract
{
    private $Handler;

    public function getHandler()
    {
        return $this->Handler;
    }

    public function setHandler(HandlerInterface $handler)
    {
        $this->Handler = $handler;
    }

}

class MyHandler extends HandlerAbstract implements HandlerInterface
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }


    public function operator()
    {
        echo $this->name . 'deal!';
        if ($this->getHandler() != null) {
            $this->getHandler()->operator();
        }
    }

}

class Test
{
    public static function main()
    {
        $H1 = new MyHandler('h1');
        $H2 = new MyHandler('h2');
        $H3 = new MyHandler('h3');
        $H1->setHandler($H2);
        $H2->setHandler($H3);

        $H1->operator();
    }

}

Test::main();
//责任链模式：有多个对象，每个对象持有下个对象的引用，这样就会形成一条链，客户端并不清除哪个对象会处理，所以，责任链模式实现了隐瞒客户端的情况下进行系统调整。
//命令只许由一个对象床底给另一个对象，而不允许传递给多个对象。


