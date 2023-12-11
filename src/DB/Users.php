<?php

namespace App\DB;

use RedBeanPHP\R;

class Users
{

    private static $table = 'users';


    /**
     * Получить юзера по его chat_id
     * @param $chat_id
     * @return \RedBeanPHP\OODBBean|NULL
     */
    static function getChat($chat_id)
    {
        return R::findOne(self::$table, 'WHERE chat_id=?', [$chat_id]);
    }


    /**
     * Добавить юзера
     * @param $chat_id
     * @param $username
     * @return void
     * @throws \RedBeanPHP\RedException\SQL
     */
    static function add($chat_id, $username = NULL)
    {

        $add = R::dispense(self::$table);
        $add->chat_id = $chat_id;
        $add->username = $username;
        R::store($add);

    }

    /**
     * Получить юзера по его id
     * @param $id
     * @return \RedBeanPHP\OODBBean|NULL
     */
    static function get($id)
    {
        return R::findOne(self::$table, 'WHERE id=?', [$id]);
    }


    /**
     * Редактируем пользователя
     * @param $user_id
     * @param $params
     * @return false|int|string
     * @throws \RedBeanPHP\RedException\SQL
     */
    static function red($user_id,$params=[]){

        if(empty($params)) return false;

        $load = R::load(self::$table,$user_id);

        foreach ($params as $key=>$value){
            $load[$key] =$value;
        }

        return R::store($load);
    }



}