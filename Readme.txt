Этапы создания Бота на php
1 : Установка php(последняя версия) + composer.


2 : DiscordPHP устанавливается с помощью Composer.
    Запуск composer require team-reflex/discord-php. Это установит последнюю версию.
    Если вы хотите, вы также можете установить ветку разработки, запустив composer require team-reflex/discord-php dev-master.
    Включите файл автозапуска композитора в верхней части вашего основного файла:
    include __DIR__.'/vendor/autoload.php';
    Сделайте бота!


3 : Создать файл php в папке где появился vandor  


4 : Базовый код - 

<?php

include __DIR__.'/vendor/autoload.php';

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

date_default_timezone_set('Europe/Moscow');

$discord = new Discord([
    'token' => '***',
]);

$discord->on('ready', function (Discord $discord) {
    echo "Бот запущен и готов к работе!", PHP_EOL;

    //Слушайте сообщения(вывод сообщений в консоль).
    $discord->on('message', function (Message $message, Discord $discord) {
        $Week = date('j F Y G:i:s');
        echo "{$Week} - {$message->author->username}: {$message->content}", PHP_EOL;
    });

});

$discord->run();

5 : Через терминал запустить командой - php "название файла" - пример : php bot.php
