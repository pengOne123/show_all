<?php

/*1.工厂方法模式 （Factory Method Pattern）       start*/

interface SenderInterface
{
    public function send();
}

//实现类
class MailSender implements SenderInterface
{
    public function send()
    {
        // TODO: Implement send() method.
        echo 'this is mailSender';
    }
}

//实现类
class SmsSender implements SenderInterface
{
    public function send()
    {
        // TODO: Implement send() method.
        echo 'this is smsSender';
    }
}

interface ProviderInterface
{
    public function produce();
}

//工厂类
class SendMailFactory implements ProviderInterface
{
    public function produce()
    {
        return new MailSender();
    }
}

class SendSmsFactory implements ProviderInterface
{
    public function produce()
    {
        return new SmsSender();
    }
}


class FactoryTest
{
    public static function main()
    {
        $SendMailFactory = new SendMailFactory();
        $SendMailFactory->produce()->send();
    }
}

FactoryTest::main();

//当增加新的聊天平台 需要增加新的工厂类即可 这样遵守了开闭原则


