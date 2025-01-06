<?php

/*14.模板方法模式 （Template Method）      start*/

abstract class CalculatorAbstract
{
    final public function calculate($exp, $opt)
    {
        $num = $this->merge($exp, $opt);
        return $this->computational($num,'100');
    }

    abstract public function computational($num1, $num2);

    public function merge($exp, $opt)
    {
        return $exp + $opt;
    }

}

class Plus extends CalculatorAbstract
{
    public function computational($num1, $num2)
    {
        return $num1 + $num2;
    }
}

class StrategyTest
{
    public static function main()
    {
        $exp = '16';
        $cal = new Plus();
        $result = $cal->calculate($exp, '1');
        echo $result;

    }

}

StrategyTest::main();

// 模板方法模式主要是在CalculatorAbstract里定义了abstract方法 利用calculate依次调用既可



