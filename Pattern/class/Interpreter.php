<?php

/*23.解释器模式（Interpreter）      start*/

namespace Interpreter;

class Context
{
    private $num1;
    private $num2;

    public function __construct($num1, $num2)
    {
        $this->num1 = $num1;
        $this->num2 = $num2;
    }

    public function getNum1()
    {
        return $this->num1;
    }


    public function setNum1($num1)
    {
        $this->num1 = $num1;
    }

    public function getNum2()
    {
        return $this->num2;
    }

    public function setNum2($num2)
    {
        $this->num2 = $num2;
    }

}

interface ExpressionInterface
{
    public function interpret(Context $context);
}

class Plus implements ExpressionInterface
{

    public function interpret(Context $context)
    {
        return $context->getNum1() + $context->getNum2();
    }

}

class Minus implements ExpressionInterface
{

    public function interpret(Context $context)
    {
        return $context->getNum1() - $context->getNum2();
    }

}

class Test
{
    public static function main()
    {
        $Minus = new Minus();
        $Plus  = new Plus();
        //计算9+2-8的值
        $result = $Minus->interpret(new Context($Plus->interpret(new Context(9, 2)), 8));

        echo $result;

    }

}

Test::main();

//解释器模式用来做各种各样的解释器，如正则表达式等的解释器等等。
