<?php
/*9.外观模式 （Facade）      start*/

class CPU
{
    public function startUp()
    {
        echo 'CPU start up' . '<br>';
    }

    public function shutDown()
    {
        echo 'CPU shotDown' . '<br>';
    }

}

class Memory
{
    public function startUp()
    {
        echo 'Memory start up' . '<br>';
    }

    public function shutDown()
    {
        echo 'Memory shotDown' . '<br>';
    }

}

class Disk
{

    public function startUp()
    {
        echo 'Disk start up' . '<br>';
    }

    public function shutDown()
    {
        echo 'Disk shotDown' . '<br>';
    }

}

class Computer
{
    private $CPU;
    private $Memory;
    private $Disk;

    public function __construct()
    {
        $this->CPU    = new CPU();
        $this->Memory = new Memory();
        $this->Disk   = new Disk;
    }


    public function startUp()
    {
        echo 'start the computer' . '<br>';
        $this->CPU->startUp();
        $this->Memory->startUp();
        $this->Disk->startUp();
        echo 'start the computer finished' . '<br>';
    }

    public function shutDown()
    {
        echo 'begin to close the computer' . '<br>';
        $this->CPU->shutDown();
        $this->Memory->shutDown();
        $this->Disk->shutDown();
        echo 'computer closed' . '<br>';
    }

}

class User
{
    public static function main()
    {
        $Computer = new Computer();
        $Computer->startUp();
        $Computer->shutDown();
    }

}

User::main();

//Computer 就是一个外观模式，如果没有Computer则CPU、Memory、Disk之间将会相互持有实例，会造成强耦合。
//适配器模式和外观模式很相似，适配器模式是后期维护做的，外观模式则是写业务逻辑的同时做的。 外观模式其实就是增加一个统一调用类。




