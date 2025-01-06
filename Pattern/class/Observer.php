<?php

/*15.观察者模式 （Observer）      start*/

interface ObserverInterface
{
    public function update();
}

class Observer1 implements ObserverInterface
{
    public function update()
    {
        echo 'Observer1 has received!';
    }
}

class Observer2 implements ObserverInterface
{
    public function update()
    {
        echo 'Observer2 has received!';
    }
}

interface SubjectInterface
{
    //增加观察者
    public function add(ObserverInterface $observer);

    //删除观察者
    public function del(ObserverInterface $observer);

    //通知所有的观察者
    public function notifyObservers();

    //自身的操作
    public function operation();

}

abstract class SubjectAbstract implements SubjectInterface
{
    private $vector = [];


    //增加观察者
    public function add(ObserverInterface $observer)
    {
        $this->vector[] = $observer;
    }

    //删除观察者
    public function del(ObserverInterface $observer)
    {
        $key = array_search($observer, $this->vector);
        if ($key !== false) array_splice($this->vector, $key, 1);
    }

    //通知所有的观察者
    public function notifyObservers()
    {
        foreach ($this->vector as &$value) {
            $value->update();
        }
    }


}

class MySubject extends SubjectAbstract
{
    public function operation()
    {
        echo 'update self!';
        $this->notifyObservers();
    }

}

class ObserverTest
{
    public static function main()
    {
        $Sub = new MySubject();
        $Sub->add(new Observer1());
        $Sub->add(new Observer2());

        $Sub->operation();
    }

}

ObserverTest::main();
//观察者模式 主要是在于添加观察者 和 观察者调用的时机









