<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.01.18
 * Time: 18:27
 */

namespace common\traits;


trait Insantiate
{
    private static $_prototype;
    public static function insantiate(){
        if(self::$_prototype === null) {
            $class = get_called_class();
            self::$_prototype = unserialize(sprintf('0:%d:"%s":0:{}',strlen($class),$class));
        }
        $entity = clone self::$_prototype;
        $entity->init();
        return $entity;
    }

}