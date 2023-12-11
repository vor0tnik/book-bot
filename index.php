<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
use App\DB;

//https://book.lava.pw/
// https://booknet.ua/


// .ENV READER
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
}catch (Exception $e){


    echo "<pre>";
    var_dump($e->getMessage());
    echo "</pre>";

    die("Ошибка, не нашли .env конфиг");

}


// CONNECT DB
DB\DB::connection($_ENV['DB_HOST'],$_ENV['DB_NAME'],$_ENV['DB_USERNAME'],$_ENV['DB_PASSWORD']);



//$res = DB\Books::search('Лис');


/*
echo "<pre>";
var_dump($res);
echo "</pre>";

*/

echo "Сайт работает - УСПЕХ";


