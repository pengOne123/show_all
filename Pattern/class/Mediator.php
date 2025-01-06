<?php

/*22.中介者模式（Mediator）      start*/

namespace Mediator;

interface MediatorInterface
{
    public function createMediator();

    public function workAll();

}

class MyMediator implements MediatorInterface
{
    private $User1;
    private $User2;

    public function getUser1()
    {
        return $this->User1;
    }

    public function getUser2()
    {
        return $this->User2;
    }

    public function createMediator()
    {
        $this->User1 = new User1($this);
        $this->User2 = new User2($this);
    }

    public function workAll()
    {
        $this->User1->work();
        $this->User2->work();
    }

}

abstract class UserAbstract
{
    private $Mediator;

    public function __construct(MediatorInterface $mediator)
    {
        $this->Mediator = $mediator;
    }

    public function getMediator()
    {
        return $this->Mediator;
    }

    abstract public function work();

}

class User1 extends UserAbstract
{

    public function __construct(MediatorInterface $mediator)
    {
        parent::__construct($mediator);
    }

    public function work()
    {
        echo 'User1 exe!';
    }

}

class User2 extends UserAbstract
{

    public function __construct(MediatorInterface $mediator)
    {
        parent::__construct($mediator);
    }

    public function work()
    {
        echo 'User2 exe!';
    }

}

class Test
{
    public static function main()
    {
        $MyMediator = new MyMediator();
        $MyMediator->createMediator();
        $MyMediator->workAll();
    }

}

Test::main();
//中介者模式也是用来降低类与类之间的耦合的，因为类与类之间有依赖关系的话，不利于功能的扩展和维护，因为只要修改一个对象，其他关联的对象都得进行修改。
//正常情况下user2持有user1实例，这样耦合度非常高，利用中介者模式进行解耦。
//使用中介者模式user1和user2只需要知道Mediator类的关联即可。


