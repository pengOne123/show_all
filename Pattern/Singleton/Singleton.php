<?php


namespace Singleton;

//����ģʽ
class Singleton
{
    //ʵ��
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