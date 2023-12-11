<?php

require_once __DIR__. '/vendor/autoload.php';

// .ENV READER
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$bot_api_key = $_ENV['TELEGRAM_TOKEN'];
$bot_username = $_ENV['TELEGRAM_NAME'];
$hook_url = 'https://' . $_SERVER['SERVER_NAME'] . '/GetHook.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

    // Set webhook
    $result = $telegram->setWebhook($hook_url);
    if ($result->isOk()) {
        echo 'WEBHOOK получен успешно';
        echo "<pre>";
        echo $result->getDescription();
        echo "</pre>";

    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo "Ошибка";
    die($e->getMessage());
}