<?php
require_once __DIR__.'/vendor/autoload.php';
use App\DB;

// .ENV READER
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();



// CONNECT DB
DB\DB::connection($_ENV['DB_HOST'],$_ENV['DB_NAME'],$_ENV['DB_USERNAME'],$_ENV['DB_PASSWORD']);







try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($_ENV['TELEGRAM_TOKEN'], $_ENV['TELEGRAM_NAME']);

    $commands_paths = ["Command/"];

    $telegram->addCommandsPaths($commands_paths);
    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
}
