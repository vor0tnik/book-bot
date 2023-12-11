<?php
namespace App\DB;
use RedBeanPHP\R;

class DB
{

    /**
     * @param $host
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return void
     */
    static function connection($host,$db_name,$db_user,$db_pass){

        $db = [
            'dsn' => 'mysql:host='.$host.';dbname='.$db_name.';charset=utf8',
            'user' => $db_user,
            'pass' => $db_pass,
        ];

        R::setup($db['dsn'], $db['user'], $db['pass']);
        if ( !R::testConnection() )
        {
            die ('Нет соединения с базой данных');
        }
        R::freeze(true);

    }

}