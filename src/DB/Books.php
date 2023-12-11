<?php

namespace App\DB;
use RedBeanPHP\R;

class Books
{


    /**
     * Имя таблицы
     * @var string
     */
    private static $table = 'books';


    /**
     * Удалить книгу
     * @param $id
     * @return void
     */
    static function delete($id){
        $load  = R::load(self::$table,$id);
        R::trash($load);
    }


    /**
     * Поиск Книг по Тайтлу и Автору
     * @param $search
     * @return \RedBeanPHP\OODBBean[]
     */
    static function search($search){


       // return R::findAll(self::$table,"where `title`  Like '%?%' OR `author` Like '%?%'",[$search]);
        return R::findAll(self::$table,"where `title`  Like '%{$search}%' OR `author` Like '%{$search}%'");

    }



    static function allGenre($genre_id){
        return R::findAll(self::$table,"where `genre`  Like '%,{$genre_id},%'");

    }




    /**
     * @param $id
     * @return \RedBeanPHP\OODBBean|NULL
     */
    static function get($id){
        return R::findOne(self::$table,'WHERE id=?',[$id]);
    }

    /**
     * Добавить книгу
     * @param $title
     * @param $description
     * @param $author
     * @param $genre
     * @param $user_add
     * @return void
     * @throws \RedBeanPHP\RedException\SQL
     */
    static function add($title,$description,$author,$genre,$user_add){

        $add = R::dispense(self::$table);
        $add->title = $title;
        $add->description = $description;
        $add->author = $author;
        $add->genre = $genre;
        $add->user_add = $user_add;
        R::store($add);

    }

    /**
     * Все книги
     * @return \RedBeanPHP\OODBBean[]
     */
    static function all(){
        return R::findAll(self::$table);
    }

    /**
     * Возвращаем все книги по лимитам(SQL)
     * @param $limit
     * @param $skip
     * @return \RedBeanPHP\OODBBean[]
     */
    static function allLimit($limit,$skip=null){
        if(empty($skip)){
            return R::findAll(self::$table,'LIMIT ?',[$limit]);
        }else{
            return R::findAll(self::$table,'LIMIT ?,?',[$skip,$limit]);
        }
    }


}