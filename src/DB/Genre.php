<?php
namespace App\DB;
use RedBeanPHP\R;
class Genre
{

    private static $table = 'genre';

    /**
     * Подгружаем по ид
     * @param $id
     * @return \RedBeanPHP\OODBBean|NULL
     */
    static function get($id){
        return R::findOne(self::$table,'WHERE id=?',[$id]);
    }


    static function getName($name){
        return R::findOne(self::$table,'WHERE name=?',[$name]);

    }


    /**
     * Добавляем жанр
     * @param $name
     * @param $return
     * @return int|string|void
     * @throws \RedBeanPHP\RedException\SQL
     */
    static function add($name,$return = null){
        $add = R::dispense(self::$table);
        $add->name = $name;
        if(empty($return)){
            R::store($add);
        }else{
            return R::store($add);
        }
    }

    /**
     * @return \RedBeanPHP\OODBBean[]
     */
    static function all(){
        return R::findAll(self::$table);
    }



}