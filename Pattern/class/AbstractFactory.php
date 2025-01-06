<?php

/*2.抽象工厂模式 （Abstract Factory Pattern）       start*/

//空调
interface AirConditionerInterface
{
    public function airConditionerInfo();
}

//风扇
interface FanInterface
{
    public function fanInfo();
}

//冰箱
interface FridgeInterface
{
    public function fridgeInfo();
}

//实现类 美的空调
class MediaAirConditioner implements AirConditionerInterface
{
    public function airConditionerInfo()
    {
        // TODO: Implement send() method.
        echo 'this is MediaAirConditioner';
    }
}

//实现类 美的风扇
class MediaAirFan implements FanInterface
{
    public function fanInfo()
    {
        // TODO: Implement send() method.
        echo 'this is MediaAirFan';
    }
}

//实现类 美的冰箱
class MediaAirFridge implements FridgeInterface
{
    public function fridgeInfo()
    {
        // TODO: Implement send() method.
        echo 'this is MediaAirFridge';
    }
}


interface FactoryInterface
{

    public function createAirConditioner();

    public function createFan();

    public function createFridge();

}

//美的工厂类
class MediaFactory implements FactoryInterface
{

    public function createAirConditioner()
    {
        return new MediaAirConditioner();
    }

    public function createFan()
    {
        return new MediaAirFan();
    }

    public function createFridge()
    {
        return new MediaAirFridge();
    }

}

//测试
class Test
{
    public static function main()
    {
        $Factory = new MediaFactory();
        $AirConditioner = $Factory->createAirConditioner();
        $AirConditioner->airConditionerInfo();
    }
}

Test::main();

//和抽象方法模式最大的区别是，每个产品类可以派生出很多产品类，如冰箱、空调、风扇，后面可以再增加洗衣机等等。工厂方法模式只有一个产品，多个平台。抽象工厂模式可以有多个产品多个平台。


