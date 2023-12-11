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


use App\Book;
use App\DB\Books;
use App\DB\Genre;
use App\DB\Users;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommands\StartCommand;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Commands\SystemCommands\GenericmessageCommand;
use function Sodium\library_version_major;

/**
 * Callback query command
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var callable[]
     */
    protected static $callbacks = [];

    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * Command execute method
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {


        $callback_query = $this->getCallbackQuery();
        $user_id = $callback_query->getFrom()->getId();
        $username = $callback_query->getFrom()->getUsername();
        $query_id = $callback_query->getId();
        $query_data = $callback_query->getData(); // имя команды


        $getUser = Users::getChat($user_id);


        switch ($query_data) { // Ищем команду


            case 'cancel': // Отмена действий


                Users::red($getUser['id'], ['action' => NULL, 'write_text' => NULL]);


                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId(),]);

                Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "🚫 Действия ввода отменили",
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ]);


                return StartCommand::MenuHome($getUser, 'Главное меню');


                break;


            case 'search_book': // Поиск книги


                Users::red($getUser['id'], ['action' => 10, 'write_text' => NULL]); // Меняем в екшене номер

                $keyboard = new InlineKeyboard();
                $keyboard->addRow(new InlineKeyboardButton(['text' => '🚫 Отмена', 'callback_data' => 'cancel']));


                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "Введите ключевое слово или фразу для поиска.\n\n<i>Поиск осуществляется по Названию книги или Автору </i>",
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


            case 'back' : //  Кнопка Назад

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId(),]);

                return StartCommand::MenuHome($getUser, 'Главное меню');

                break;


            case 'add_book': // Добавляем Книгу

                /**
                 * [action]
                 * 1 -- вод названия для книги
                 * 2 -- вод Автор для книги
                 * 3 -- ввод жанра
                 * 4 -- ввод Содержания
                 *
                 * 10 - поиск книг
                 */


                Users::red($getUser['id'], ['action' => 1, 'write_text' => json_encode([])]);

                $keyboard = new InlineKeyboard();


                $keyboard->addRow(new InlineKeyboardButton(['text' => '🚫 Отмена', 'callback_data' => 'cancel']));

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "Введите <b>Название Книги</b>:",
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


            case $this->checkdelBook($query_data): // Удаляем книгу
                $id_book = str_replace('delBook_', '', $query_data);

                $getBook = Books::get($id_book);

                if (empty($getBook)) {
                    return $callback_query->answer([
                        'text' => 'К сожалению книга не доступна',
                        'show_alert' => true,
                    ]);
                }


                Books::delete($getBook['id']);

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "✅ Удалили книгу: <code>{$getBook['title']}</code>",
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ]);


                return StartCommand::MenuHome($getUser, 'Главное меню');


                break;


            case $this->checkviewGenre($query_data): // Выбрали Книги по Жанру

                $id_genre = str_replace('viewGenre_', '', $query_data);


                $getBooks = Books::allGenre($id_genre);

                $getGenre = Genre::get($id_genre);


                if (empty($getBooks)) {
                    return $callback_query->answer([
                        'text' => 'По данному жанру, книг нету (:',
                        'show_alert' => true,
                    ]);
                }


                $keyboard = new InlineKeyboard();

                foreach ($getBooks as $book) {
                    $keyboard->addRow(new InlineKeyboardButton(['text' => "📖 {$book['title']}({$book['author']})", 'callback_data' => 'viewBook_' . $book['id']]));
                }

                $keyboard->addRow(new InlineKeyboardButton(['text' => '🔙 Главное меню', 'callback_data' => 'back']));

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "Книги по жанру: {$getGenre['name']}",
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


            case $this->checkviewBook($query_data): //Читаем клик по книге


                $id_book = str_replace('viewBook_', '', $query_data);

                $getBook = Books::get($id_book);

                if (empty($getBook)) {
                    return $callback_query->answer([
                        'text' => 'К сожалению книга не доступна',
                        'show_alert' => true,
                    ]);
                }


                $book_genre = '';

                //** Берем все названия жанров */
                $allGenre = Genre::all(); // обьект с жанрами
                $arr_genre = explode(',', $getBook['genre']);

                foreach ($arr_genre as $genre_id) {

                    if (!empty($allGenre[$genre_id])) {

                        if (empty($genre_id)) continue;

                        $book_genre .= " #{$allGenre[$genre_id]['name']}";

                    }

                }

                $msg = "<b>Название:</b>{$getBook['title']}" . PHP_EOL;
                $msg .= "<b>Автор:</b>{$getBook['author']}" . PHP_EOL;
                $msg .= "<b>Жанр:</b>{$book_genre}" . PHP_EOL;
                $msg .= "<b>Содержания:</b>" . PHP_EOL;
                $msg .= "{$getBook['description']}" . PHP_EOL;


                $keyboard = new InlineKeyboard();
                $keyboard->addRow(new InlineKeyboardButton(['text' => '🗑 Удалить книгу', 'callback_data' => 'delBook_' . $getBook['id']]));
                $keyboard->addRow(new InlineKeyboardButton(['text' => '🔙 Главное меню', 'callback_data' => 'back']));


                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => $msg,
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


            case 'list_book': // Кнопка посмотреть все книги

                $books = Books::all();


                $count_book = count($books);


                $keyboard = new InlineKeyboard();


                if (!empty($books)) {
                    foreach ($books as $book) {
                        $keyboard->addRow(new InlineKeyboardButton(['text' => "📖 {$book['title']}({$book['author']})", 'callback_data' => 'viewBook_' . $book['id']]));
                    }
                }


                $keyboard->addRow(new InlineKeyboardButton(['text' => '🔙 Назад', 'callback_data' => 'back']));

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "По вашему запросу мы нашли {$count_book}шт.",
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


            case 'genre_book': // Список категорий


                $genres = Genre::all();
                if (empty($genres)) {
                    return $callback_query->answer([
                        'text' => 'На данный момент жанры отсутствуют',
                        'show_alert' => true,
                    ]);
                }


                $keyboard = new InlineKeyboard();


                foreach ($genres as $genre) {

                    $keyboard->addRow(new InlineKeyboardButton(['text' => "📓 {$genre['name']}", 'callback_data' => 'viewGenre_' . $genre['id']]));

                }

                $keyboard->addRow(new InlineKeyboardButton(['text' => '🔙 Назад', 'callback_data' => 'back']));

                Request::deleteMessage(['chat_id' => $user_id, 'message_id' => $callback_query->getMessage()->getMessageId()]); // Удаляем прошлое сообщения

                return Request::sendMessage([
                    'chat_id' => $user_id,
                    'text' => "Выберите категорию",
                    'parse_mode' => 'HTML',
                    'reply_markup' => $keyboard,
                    'disable_web_page_preview' => true
                ]);


                break;


        }


        return Request::emptyResponse();
    }


    /**
     * Add a new callback handler for callback queries.
     *
     * @param $callback
     */
    public static function addCallbackHandler($callback): void
    {
        self::$callbacks[] = $callback;
    }


    /**
     * Поиск команды viewBook_...
     * @param $quer
     * @return bool
     */
    public function checkviewBook($quer)
    {
        if (stripos($quer, "viewBook_") === false) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Поиск команды delBook_...
     * @param $quer
     * @return bool
     */
    public function checkdelBook($quer)
    {
        if (stripos($quer, "delBook_") === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Поиск команды viewGenre_
     * @param $quer
     * @return bool
     */
    public function checkviewGenre($quer)
    {
        if (stripos($quer, "viewGenre_") === false) {
            return false;
        } else {
            return true;
        }
    }


}