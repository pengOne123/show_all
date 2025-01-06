<?php


namespace Singleton;

//单例模式
class Singleton
{
    //实例
    private static $instance = [];

    public static function getInstance($path)
    {
        if (isset(self::$instance[$path])) {
            return self::$instance[$path];
        }
        self::$instance[$path] = new $path();
        return self::$instance[$path];
    }
}