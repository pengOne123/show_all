<?php
/*4.建造者模式 （Builder）       start*/

//人类属性
class Human
{
    public $head;
    public $body;
    public $hand;
    public $foot;

    //----
    public function getHead()
    {
        return $this->head;
    }

    public function setHead($head)
    {
        $this->head = $head;
    }

    //----
    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    //----
    public function getHand()
    {
        return $this->hand;
    }

    public function setHand($hand)
    {
        $this->hand = $hand;
    }

    //----
    public function getFoot()
    {
        return $this->foot;
    }

    public function setFoot($foot)
    {
        $this->foot = $foot;
    }

}

//建造人类的接口
interface IBuildHuman
{
    public function buildHead();

    public function buildBody();

    public function buildHand();

    public function buildFoot();

    public function createHuman();
}

//聪明的人建造实现
class SmartManBuilder implements IBuildHuman
{
    private $human;

    public function SmartManBuilder()
    {
        $this->human = new Human();
    }

    public function buildHead()
    {
        // TODO: Implement buildHead() method.
        $this->human->setHead('智商180的头脑');
    }

    public function buildBody()
    {
        // TODO: Implement buildBody() method.
        $this->human->setBody('新的身体');
    }

    public function buildHand()
    {
        // TODO: Implement buildHand() method.
        $this->human->setHand('新的手');
    }

    public function buildFoot()
    {
        // TODO: Implement buildFoot() method.
        $this->human->setFoot('新的脚');
    }

    public function createHuman()
    {
        // TODO: Implement createHuman() method.
        return $this->human;
    }

}

//建造流程
class Director
{
    public function createHumanByDirector(IBuildHuman $bh)
    {
        $bh->buildBody();
        $bh->buildFoot();
        $bh->buildHand();
        $bh->buildHead();
        return $bh->createHuman();
    }
}

//建造测试
class BuilderTest
{
    public static function main()
    {
        $Director = new Director();
        $human    = $Director->createHumanByDirector(new SmartManBuilder());
        echo $human->head . '<br>';
        echo $human->body . '<br>';
        echo $human->hand . '<br>';
        echo $human->foot . '<br>';
    }
}

BuilderTest::main();
//tips:此模式主要类 是Director  主要解决建造流程的问题


