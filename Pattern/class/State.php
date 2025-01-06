<?php
/*16.迭代子模式 （Iterator）      start*/

class State
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

    public function method1()
    {
        echo 'execute the first opt!';
    }

    public function method2()
    {
        echo 'execute the second opt!';
    }

}

class Context
{
    private $State;

    public function __construct(State $state)
    {
        $this->State = $state;
    }

    public function getState()
    {
        return $this->State;
    }

    public function setState(State $state)
    {
        $this->State = $state;
    }

    public function method()
    {
        if ($this->State->getValue() == 'state1') {
            $this->State->method1();
        } elseif ($this->State->getValue() == 'state2') {
            $this->State->method2();
        }
    }

}

class Test
{
    public static function main()
    {
        $State   = new State();
        $Context = new Context($State);

        //设置第一种状态
        $State->setValue('state1');
        $Context->method();

        //设置第二种状态
        $State->setValue('state2');
        $Context->method();

    }
}


Test::main();

//状态模式（状态机）





