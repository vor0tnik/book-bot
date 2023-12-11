<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;


use App\DB\Books;
use App\DB\Genre;
use App\DB\Users;
use Cassandra\Set;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommands\StartCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Symfony\Component\VarDumper\Caster\ResourceCaster;

//use Users;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * @var bool
     */
    protected $need_mysql = false;

    /**
     * Execution if MySQL is required but not available
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function executeNoDb(): ServerResponse
    {
        // Try to execute any deprecated system commands.
        if (self::$execute_deprecated && $deprecated_system_command_response = $this->executeDeprecatedSystemCommand()) {
            return $deprecated_system_command_response;
        }

        return Request::emptyResponse();
    }

    /**
     * Execute command
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $t_msg = $message->getText();

        $chat_id = $message->getChat()->getId();

        $username = $message->getChat()->getUsername(); // адрес

        $getUser = Users::getChat($chat_id);


        if (!empty($getUser['action'])) {


            switch ($getUser['action']) {

                case 1: // title

                    $js = ['title' => $t_msg];

                    Users::red($getUser['id'], ['action' => 2, 'write_text' => json_encode($js, 128)]);

                    $keyboard = new InlineKeyboard();


                    $keyboard->addRow(new InlineKeyboardButton(['text' => '🚫 Отмена', 'callback_data' => 'cancel']));

                    return Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Введите <b>Автора Книги</b>:",
                        'parse_mode' => 'HTML',
                        'reply_markup' => $keyboard,
                        'disable_web_page_preview' => true
                    ]);


                    break;


                case 2: // author


                    $js = json_decode($getUser['write_text'], true); // подгрузили json что ранее вводили

                    $genreAll = Genre::all(); // Подгрузили все Жанры


                    $js['author'] = $t_msg; // Добавляем в массив Автора




                    Users::red($getUser['id'], ['action' => 3, 'write_text' => json_encode($js, 128)]);


                    $keyboard = new InlineKeyboard();
                    $keyboard->addRow(new InlineKeyboardButton(['text' => '🚫 Отмена', 'callback_data' => 'cancel']));

                    $str_genre = '';

                    if(!empty($genreAll)){
                        foreach ($genreAll as $genre){
                            $str_genre .= "<code>{$genre['name']},</code>  ";
                        }
                    }







                    return Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Введите <b>Жанр Книги</b>:\n<b>Все доступные жанры:</b> {$str_genre}",
                        'parse_mode' => 'HTML',
                        'reply_markup' => $keyboard,
                        'disable_web_page_preview' => true
                    ]);

                    break;


                case 3: // genre
                    $js = json_decode($getUser['write_text'], true);

                    //  $js['genre'] = $t_msg;



                    $genre_arr = explode(',', $t_msg);

                    $ids_genre = []; // Массив с ID жанров

                    foreach ($genre_arr as $name_genre) {
                        if (empty($name_genre)) continue;
                        $name_genre = trim($name_genre);
                        $getGenre = Genre::getName($name_genre);
                        if (empty($getGenre)) {
                            $id_genre = Genre::add($name_genre, 1);
                        } else {
                            $id_genre = $getGenre['id'];
                        }


                        $ids_genre[] = $id_genre;

                    }


                    $ids_genre_str = implode(',', $ids_genre);

                    $js['genre'] = ','.$ids_genre_str.','; // Поставили с обеих сторон запятую чтобы потом делать поиск через LIKE


                    Users::red($getUser['id'], ['action' => 4, 'write_text' => json_encode($js, 128)]);


                    $keyboard = new InlineKeyboard();
                    $keyboard->addRow(new InlineKeyboardButton(['text' => '🚫 Отмена', 'callback_data' => 'cancel']));


                    return Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Введите <b>Содержания Книги</b>:",
                        'parse_mode' => 'HTML',
                        'reply_markup' => $keyboard,
                        'disable_web_page_preview' => true
                    ]);


                    break;


                case 4:
                    $js = json_decode($getUser['write_text'], true);


                    Books::add($js['title'], $t_msg, $js['author'], $js['genre'], $getUser['id']);

                    Users::red($getUser['id'], ['action' => NULL, 'write_text' => NULL]);


                    Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "✅ Книга была добавлена ✅",
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true
                    ]);


                    return StartCommand::MenuHome($getUser, 'Главное меню');


                    break;


                case 10: // Поиск по фразе

                    $searchBooks = Books::search($t_msg);

                    if (empty($searchBooks)) {
                        Users::red($getUser['id'], ['action' => NULL]);
                        return StartCommand::MenuHome($getUser, 'По вашему запросу нету результатов 😢');
                    }


                    $count_book = count($searchBooks);

                    $keyboard = new InlineKeyboard();


                    foreach ($searchBooks as $book) {
                        $keyboard->addRow(new InlineKeyboardButton(['text' => "📖 {$book['title']}({$book['author']})", 'callback_data' => 'viewBook_' . $book['id']]));
                    }


                    $keyboard->addRow(new InlineKeyboardButton(['text' => '🔙 Назад', 'callback_data' => 'back']));

                    return Request::sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "По вашему запросу мы нашли {$count_book}шт.",
                        'parse_mode' => 'HTML',
                        'reply_markup' => $keyboard,
                        'disable_web_page_preview' => true
                    ]);

                    break;


            }


        }


        //$t_msg

        return Request::emptyResponse();


    }


}
