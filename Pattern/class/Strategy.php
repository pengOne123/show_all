<?php

/*13.策略模式 （Strategy）      start*/

interface ICalculatorInterface
{
    public function calculate($exp);
}

abstract class CalculatorAbstract
{
    public function merge($one, $two)
    {
        return $one . $two;
    }
}

class Plus extends CalculatorAbstract implements ICalculatorInterface
{
    public function calculate($exp)
    {
        return $this->merge($exp, '+');
    }

}

class Minus extends CalculatorAbstract implements ICalculatorInterface
{
    public function calculate($exp)
    {
        return $this->merge($exp, '-');
    }

}

class Multiply extends CalculatorAbstract implements ICalculatorInterface
{
    public function calculate($exp)
    {
        return $this->merge($exp, '*');
    }
}


class StrategyTest
{
    public static function main()
    {
        $num        = 10;
        $Arithmetic = new Plus();
        $result     = $Arithmetic->calculate($num);
        echo $result;
    }
}

StrategyTest::main();

//策略模式的决定权在于用户（客户端），系统本身提供不同的算法的实现。因此，策略模式多用在算法决策系统中，外部用户只需要决定要用哪个算法即可。