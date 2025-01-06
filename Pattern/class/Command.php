<?php
/*18.命令模式 （Command）      start*/

interface CommandInterface
{
    public function exe();
}

class MyCommand implements CommandInterface
{
    private $Receiver;

    public function __construct(Receiver $receiver)
    {
        $this->Receiver = $receiver;
    }

    public function exe()
    {
        $this->Receiver->action();
    }

}

class Receiver
{
    public function action()
    {
        echo 'command received!';
    }
}

class Invoker
{
    private $Command;

    public function __construct(CommandInterface $command)
    {
        $this->Command = $command;
    }

    public function action()
    {
        $this->Command->exe();
    }

}

class Test
{
    public static function main()
    {
        $Receiver = new Receiver();//执行者
        $Cmd      = new MyCommand($Receiver);//执行
        $Invoker  = new Invoker($Cmd);//调用者
        $Invoker->action();
    }

}

Test::main();
//命令模式的目的是达到命令的发出者和执行者之间的解耦，实现请求和执行分开。
