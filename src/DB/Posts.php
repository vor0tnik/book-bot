<?php
namespace App\DB;
use RedBeanPHP\R;

class Posts
{

    private static $table = 'posts';

    static function get(){
        return R::findOne(self::$table,'WHERE id > ?',[1]);
    }


}