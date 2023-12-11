<?php

/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;


//use Grpc\Channel;
use App\DB\Users;
use Longman\TelegramBot\Commands\SystemCommands\CallbackqueryCommand;
use Longman\TelegramBot\Commands\UserCommand;
//use Longman\TelegramBot\Entities\ChannelPost;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\KeyboardButtonPollType;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
//use Longman\TelegramBot\Entities\Chat;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ChatMember;



/**
 * Start command
 */
class StartCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start';

    /**
     * @var string
     */
    protected $version = '1.2.0';


    public function execute(): ServerResponse
    {



        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();

        $username =  $message->getChat()->getUsername(); // @username
        if(empty($username))$username = NULL;



        $getUser = Users::getChat($chat_id);
        if(empty($getUser)){ // если юзера нету то мы его добавляем в БД и подгружаем
            Users::add($chat_id,$username);
            $getUser = Users::getChat($chat_id);
        }

        return self::MenuHome($getUser,"Приветствуем тебя {$username}\n Наш бот поможет тебе найти нужную книгу");



    }


    static public function MenuHome($getUser,$text = 'Главное меню'){
        // Генерируем клавиатуру
        $keyboard = new InlineKeyboard();
        $keyboard->addRow(new InlineKeyboardButton(['text'=>'📚 Посмотреть все книги','callback_data'=>'list_book']));
        $keyboard->addRow(new InlineKeyboardButton(['text'=>'🔎 Найти книгу','callback_data'=>'search_book']));
        $keyboard->addRow(new InlineKeyboardButton(['text'=>'📓 Все жанры','callback_data'=>'genre_book']));
        $keyboard->addRow(new InlineKeyboardButton(['text'=>'➕ Добавить книгу','callback_data'=>'add_book']));




        return Request::sendMessage([
            'chat_id'=>$getUser['chat_id'],
            'text'=>$text,
            'parse_mode'=>'HTML',
            'reply_markup'=>$keyboard,
            'disable_web_page_preview'=>true
        ]);
    }



    public function IsChatMemberUser($canal,$user_id){

        $get_tg =  Request::getChatMember(['chat_id'=> $canal, 'user_id'=> $user_id]);

        if(empty($get_tg)) return false;

        if(!is_array($get_tg))$get_tg = json_decode($get_tg,true);

        if(empty($get_tg['result'])) return false;

        if ($get_tg['result']['status'] == 'left') return false;

        return true;


    }







}
