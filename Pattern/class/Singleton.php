<?php
/*3.单例模式 （Singleton）       start*/
class Singleton
{
    //实例
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            echo 'new'.PHP_EOL;
            self::$instance = new Login();
        }
        return self::$instance;
    }

}

class Login
{
    public $loginId;

}

var_dump(Singleton::getInstance()->loginId);
Singleton::getInstance()->loginId = 1;
var_dump(Singleton::getInstance()->loginId);




