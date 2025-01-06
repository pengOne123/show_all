<?php
/*19.备忘录模式（Memento）      start*/

class Original
{
    private $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function createMemento()
    {
        return new Memento($this->value);
    }

    public function restoreMemento(Memento $memento)
    {
        $this->value = $memento->getValue();
    }

}

class Memento
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

}

class Storage
{
    private $Memento;

    public function __construct(Memento $memento)
    {
        $this->Memento = $memento;
    }

    public function getMemento()
    {
        return $this->Memento;
    }

    public function setMemento(Memento $memento)
    {
        $this->Memento = $memento;
    }

}

class Test
{
    public static function main()
    {
        //创建原始类
        $Origin = new Original();
        $Origin->setValue('egg');

        //创建备忘录
        $Storage = new Storage($Origin->createMemento());

        //修改原始类的状态
        echo '初始化状态为：'.$Origin->getValue();
        echo '<br>';

        $Origin->setValue('niu');
        echo '修改后的状态为：'.$Origin->getValue();
        echo '<br>';

        //恢复原始类的状态
        $Origin->restoreMemento($Storage->getMemento());

        echo '恢复后的状态为：'.$Origin->getValue();
        echo '<br>';

    }

}

Test::main();

//这个模式 做了‘备份->恢复’

