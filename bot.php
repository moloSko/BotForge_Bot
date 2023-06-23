<?php

include __DIR__ . '/vendor/autoload.php';

// require "redbd.php";

/*--Основные--*/
use Discord\Discord;
use Discord\DiscordCommandClient;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Discord\WebSockets\Intents;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
/*--Роли--*/
use Discord\Parts\Guild\Guild;
use Discord\Parts\Guild\Role;
use Discord\Parts\User\Member;
/*------Эмбед Сообщение-------*/
use Discord\Parts\Embed\Embed;
/*-----Вэб--------------*/
use React\Http\Browser;
use React\EventLoop\Factory;
use Psr\Http\Message\ResponseInterface;
use Discord\Builders\MessageBuilder;
/*--Кнопки--*/
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Helpers\Collection;
/*----------Таймер----------*/
use React\EventLoop\Loop;
use React\EventLoop\LoopInterface;
/*-----Активность бота--------------*/
use Discord\Parts\User\Activity;
/*--Формы - Modals & Text Inputs--*/
use Discord\Builders\Components\TextInput;
/*--Меню--*/
use Discord\Builders\Components\SelectMenu;
use Discord\Builders\Components\Option;

$loop = Factory::create();
$browser = new Browser($loop);

$discord = new Discord([
    'token' => 'OTY0NDc1NTQ1NTk5NDc1NzYy.YllL2Q.cwbCDPhssJfD3vrxPlOL-sUAf-g',
    'loadAllMembers' => true,
    'intents' => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS,
    'loop' => $loop,
]);

$discord->on('ready', function (Discord $discord) use ($browser){
    echo "Бот запущен и готов к работе!", PHP_EOL;

    $game = new Activity($discord, ['name' => 'Посмотреть мои команды /помощь', 'type' => Activity::TYPE_PLAYING]);
    $discord->updatePresence($game, true, Activity::STATUS_ONLINE);

    $discord->on(Event::INTERACTION_CREATE, function ($interaction, $discord){
        if ($interaction->data->custom_id === "create_ticket") {
          $guild = $interaction->guild;
          $user = $interaction->member;
          $ticketId = rand(13928, 53902);
          $newchannel = $interaction->guild->channels->create([
              'name' => '🔓 Тикет -' . $ticketId,
              'type' => '0',
              'topic' => 'Тикет открыт',
              'parent_id' => '1093159619108556810',
              'nsfw' => false
          ]);
          $interaction->guild->channels->save($newchannel)->then(function ($newchannel) use ($user, $interaction, $discord){
            $newchannel->setPermissions($user, ['send_messages', 'view_channel', 'add_reactions', 'read_message_history', 'attach_files']);
            $interaction->respondWithMessage(MessageBuilder::new()->setContent("Тикет создан - перейдите в канал <#{$newchannel->id}>!"), true);
            $informmessage = new Embed($discord);
            $informmessage->setColor('#290000');
            $informmessage->setDescription("```BotForge | Поддержка```\n__Как сообщить о проблеме__\n\n**ФОРМА:**\n```1. Тема вашего обращение - ЖАЛОБЫ | ВОПРОС | ДРУГОЕ.\n2. Описание вашего обращения.```\n\nЕсли вы создали этот тикет по ошибке, нажмите кнопку **\"Закрыть тикет\"**\nВаш тикет в очереди на решение, ожидайте!");
            $builder = MessageBuilder::new();
            $builder->addEmbed($informmessage);
            $actionRow = ActionRow::new();
            $closeticket = Button::new(Button::STYLE_DANGER);
            $closeticket->setLabel('Закрыть тикет');
            $closeticket->setCustomId("close_ticket");
            $actionRow->addComponent($closeticket);
            $builder->addComponent($actionRow);
            $newchannel->sendMessage($builder);
          });
        };
        if($interaction->data->custom_id === "close_ticket"){
          $guild = $interaction->guild;
          $user = $interaction->member;
          $channel = $discord->getChannel($interaction->channel_id);
          $namechan = substr($channel->name, 5);
          $interaction->respondWithMessage(MessageBuilder::new()->setContent("🔒 Тикет закрыть. Рад был помочь!"));
          $channel->setPermissions($user, [''])->then(function($rename_ch) use ($channel, $namechan, $guild){
            $channel->name = "🔒 {$namechan}";
            $guild->channels->save($channel);
          });
        };
        if ($interaction->data->custom_id === "frequent_questions") {
            $iformmenu = MessageBuilder::new();
            $selectmenu = SelectMenu::new();    
            $helpinfo = Option::new('1️⃣ Как получить роль Разработчик?', 'helproleR');
            $helpinfobot = Option::new('2️⃣ Когда тесты ботов?', 'helpbot');
            $selectmenu->addOption($helpinfo);
            $selectmenu->addOption($helpinfobot);
            $iformmenu->addComponent($selectmenu);
            $interaction->respondWithMessage($iformmenu, true);
            $selectmenu->setListener(function ($interaction, Collection $options){         
              if ($options[0]->getValue() == 'helproleR') {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("**```Для получение одной из ролей #Разработчик необходимо:```**\n1. Создать тикет в канале <#971401999474118666> (нажав на `🏷️ Создать тикет!`).\n2. Написать форму (образец будет после нажатия кнопки `🏷️ Создать тикет!`).\n3. Ожидайте когда ответит <@&964518359058235413>/<@&981030940065296416> (не более 24ч)."), true);                                         
              }
              if ($options[0]->getValue() == 'helpbot') {
                $interaction->respondWithMessage(MessageBuilder::new()->setContent("Вопросов нет!"), true);
              }
            }, $discord);
        };
    });

    $discord->getLoop()->addPeriodicTimer(21600, function ($timer) use ($discord, $browser) {
        try{
            $i = 0;
            $nsfw = $discord->getChannel('977619966041944134');
            $sfw = $discord->getChannel('1014122517633441862');
            $neko = $discord->getChannel('1014122859720871976');
            $trap = $discord->getChannel('1014122909129781338');
            $blowjop = $discord->getChannel('1014123019851022356');
            while ($i != 10){
                $browser->get('https://api.waifu.im/search/?included_tags=hentai')->then(function (ResponseInterface $response) use ($discord, $nsfw) {
                    $animwaifus = json_decode($response->getBody())->images[0]->url;
                    $nsfw->sendMessage(MessageBuilder::new()->setContent($animwaifus));
                });
                $browser->get('https://api.waifu.pics/nsfw/waifu')->then(function (ResponseInterface $response) use ($discord, $nsfw) {
                    $animwaifu = json_decode($response->getBody())->url;
                    $nsfw->sendMessage(MessageBuilder::new()->setContent($animwaifu));
                });
                $browser->get('https://api.waifu.pics/sfw/waifu')->then(function (ResponseInterface $response) use ($discord, $sfw) {
                    $sfwanimwaifu = json_decode($response->getBody())->url;
                    $sfw->sendMessage(MessageBuilder::new()->setContent($sfwanimwaifu));
                });
                $browser->get('https://api.waifu.pics/nsfw/neko')->then(function (ResponseInterface $response) use ($discord, $neko) {
                    $nekoanimwaifu = json_decode($response->getBody())->url;
                    $neko->sendMessage(MessageBuilder::new()->setContent($nekoanimwaifu));
                });
                $browser->get('https://api.waifu.pics/nsfw/trap')->then(function (ResponseInterface $response) use ($discord, $trap) {
                    $trapanimwaifu = json_decode($response->getBody())->url;
                    $trap->sendMessage(MessageBuilder::new()->setContent($trapanimwaifu));
                });
                $browser->get('https://api.waifu.im/search/?included_tags=oral')->then(function (ResponseInterface $response) use ($discord, $blowjop) {
                    $bljanimwaifus = json_decode($response->getBody())->images[0]->url;
                    $blowjop->sendMessage(MessageBuilder::new()->setContent($bljanimwaifus));
                });
                $browser->get('https://api.waifu.pics/nsfw/blowjob')->then(function (ResponseInterface $response) use ($discord, $blowjop) {
                    $bljanimwaifu = json_decode($response->getBody())->url;
                    $blowjop->sendMessage(MessageBuilder::new()->setContent($bljanimwaifu));
                });
                $i++;
            };
        } catch (Exception $e){
          echo 'Исключение отправки сообщения NSFW: ',  $e->getMessage(), "\n";
        };
    });
});

$discord->on(Event::GUILD_MEMBER_ADD, function (Member $member, Discord $discord) {
    if ($member->guild->id == '964517705791184986') {
        $member->addRole('964518066832691230');
        $welkember = new Embed($discord);
        $welkember->setColor('#5ce600');
        $welkember->setDescription("Приветствуем тебя" . $member . "!");
        $discord->getChannel('964517710832734251')->sendEmbed($welkember);
    };
});

$discord->on('message', function (Message $message, Discord $discord) use ($browser) {
    if (mb_strtolower($message->content) == '!nsfwаним') {
        if (($message->channel->nsfw) == 1) {
            $browser->get('https://api.waifu.pics/nsfw/waifu')->then(function (ResponseInterface $response) use ($discord, $message) {
                $animwaifu = json_decode($response->getBody())->url;
                $message->channel->sendMessage(MessageBuilder::new()->setContent($animwaifu))->then(function ($delete_message) use ($message) {
                    $message->delete();
                    return true;
                });
            });
        } else {
            $cheacknsfwembd = new Embed($discord);
            $cheacknsfwembd->setColor('#8b2323');
            $cheacknsfwembd->setDescription("**Данный канал не предназначен для этой команды! (требуется канал NSWF)**");
            $message->channel->sendMessage(MessageBuilder::new()->addEmbed($cheacknsfwembd));
        };
    };
    if (mb_strtolower($message->content) == '!sfwаним') {
        $browser->get('https://api.waifu.pics/sfw/waifu')->then(function (ResponseInterface $response) use ($discord, $message) {
            $sfwanimwaifu = json_decode($response->getBody())->url;
            $message->channel->sendMessage(MessageBuilder::new()->setContent($sfwanimwaifu))->then(function ($delete_message) use ($message) {
                $message->delete();
                return true;
            });
        });
    };
    if (mb_strtolower($message->content) == '!nekoаним') {
        if (($message->channel->nsfw) == 1) {
            $browser->get('https://api.waifu.pics/nsfw/neko')->then(function (ResponseInterface $response) use ($discord, $message) {
                $nekoanimwaifu = json_decode($response->getBody())->url;
                $message->channel->sendMessage(MessageBuilder::new()->setContent($nekoanimwaifu))->then(function ($delete_message) use ($message) {
                    $message->delete();
                    return true;
                });
            });
        } else {
            $cheacknsfwembd = new Embed($discord);
            $cheacknsfwembd->setColor('#8b2323');
            $cheacknsfwembd->setDescription("**Данный канал не предназначен для этой команды! (требуется канал NSWF)**");
            $message->channel->sendMessage(MessageBuilder::new()->addEmbed($cheacknsfwembd));
        };
    };
    if (mb_strtolower($message->content) == '!trapаним') {
        if (($message->channel->nsfw) == 1) {
            $browser->get('https://api.waifu.pics/nsfw/trap')->then(function (ResponseInterface $response) use ($discord, $message) {
                $trapanimwaifu = json_decode($response->getBody())->url;
                $message->channel->sendMessage(MessageBuilder::new()->setContent($trapanimwaifu))->then(function ($delete_message) use ($message) {
                    $message->delete();
                    return true;
                });
            });
        } else {
            $cheacknsfwembd = new Embed($discord);
            $cheacknsfwembd->setColor('#8b2323');
            $cheacknsfwembd->setDescription("**Данный канал не предназначен для этой команды! (требуется канал NSWF)**");
            $message->channel->sendMessage(MessageBuilder::new()->addEmbed($cheacknsfwembd));
        };
    };
    if (mb_strtolower($message->content) == '!bljаним') {
        if (($message->channel->nsfw) == 1) {
            $browser->get('https://api.waifu.pics/nsfw/blowjob')->then(function (ResponseInterface $response) use ($discord, $message) {
                $bljanimwaifu = json_decode($response->getBody())->url;
                $message->channel->sendMessage(MessageBuilder::new()->setContent($bljanimwaifu))->then(function ($delete_message) use ($message) {
                    $message->delete();
                    return true;
                });
            });
        } else {
            $cheacknsfwembd = new Embed($discord);
            $cheacknsfwembd->setColor('#8b2323');
            $cheacknsfwembd->setDescription("**Данный канал не предназначен для этой команды! (требуется канал NSWF)**");
            $message->channel->sendMessage(MessageBuilder::new()->addEmbed($cheacknsfwembd));
        };
    };
    if ($message->member != null){
        if (($message->member->getPermissions()->administrator) == 1) {
            if (mb_strtolower($message->content) == '!emb1') {
                $LogoEmbededMessage = new Embed($discord);
                $LogoEmbededMessage->setColor('#946D13');
                $LogoEmbededMessage->setImage('https://cdn.discordapp.com/attachments/1049567414842572881/1049567466382176296/prav.png');
                $PravEmbededMessage = new Embed($discord);
                $PravEmbededMessage->setColor('#946D13');
                $PravEmbededMessage->setDescription("**1.Общие положения🤖** ```fix\n-Участники сервера Дискорд равны перед правилами вне зависимости от опыта и роли.\n-Мат разрешается, но без злоупотребления.\n-Запрещено оскорбление других пользователей.\n-Нельзя использовать NSFW: шок-контент, порнографию(Исключение: каналы предназначенные для NSFW контента).\n-Запрещено злоупотребление Caps Lock.\n-Запрещены все типы флуда.\n-Запрещается жестки троллинг.\n```\n\n**2.Размещение ссылок:postbox:** ```fix\n-Запрещается реклама без согласования с администратором.\n-Не допускается спам-рассылка в личных СМС с другими пользователями.\n-Нельзя кидать ссылки с доменами на Ютуб, ВК, Роблокс и Вики.\n-Размещение ссылки по согласованию с администратором/модератором.```\n\n**3.Голосовой чат:microphone2:** ```fix\n-Нельзя включать музыку в микрофон(Исключение: Музыкальный канал).\n-Не допускается издание громких звуков в микрофон.\n-При наличии шума вокруг рекомендуется применение Push-To-Talk.```\n\n**4.Ники и аватарки:mirror:** ```fix\n-Администратор вправе требовать изменение ника и картинки, если считает, что они оскорбляют кого-либо.\n-Запрещены ники типа User, Discord User, NickName и прочие, в том числе Admin, Moderator и т. д.\n-Запрещено использование имен с матом, оскорблением, религиозными названиями, рекламой, пропагандой алкоголя / наркотиков.\n-Не допускается применение символики террористов и запрещенных организации, призыв к насилию и экстремизму.\n-Нельзя использовать бессмысленный набор символов с многократным повторением одной или нескольких букв подряд.\n-Не допускаются картинки с ненормативной лексикой, оскорблением и прочими запрещенными вещами, о которых упоминалось выше.```\n\n**5.Правила Дискорд по применению каналов и подканалов:tv: ** ```fix\n-На название канала распространяются те же требования, что и для сервера Дискорд.\n-В любом канале / подканале запрещена публикация ссылок на донат-сайты, площадки приема платежей, спонсорской помощи, пожертвований и других сервисов.```\n\n**6.Получение __уникальных__ ролей**🏹```fix\n-Для получение роли:```<@&981024614366978148>\n```fix\nТребуется создать тикет в```<#971401999474118666>\n\n```fix\n(В нём описать чем занимаетесь и почему вам требуется данная роль, по желанию можно указать ссылку на GitHub) и ожидать ответа Администратора/Модератора сервера.```\n\n```fix\n-Для получение роли:```<@&968198654978580550>\n```fix\nВо время теста бота/программ/сервера в играх и т.д. Будет открыт доступ на отдельный тестовый канал где можно будет оставить заявку на получениние данной роли(Выдаётся только на время теста! После будет снята автоматически)```\n\n**7.Ответственность:knot:**```fix\n-При нарушении правил сервера Дискорд принимаются меры к пользователям вплоть до ограничения доступа.\n-Обход бана путем входа под другим идентификатором или иными путями — бан.\n-Администратор сервера вправе отказать в доступе любому участнику.\n-Администратор не обязан указывать причины или предупреждать об этом.\n-Нарушение упомянутых выше норм — бан.\n-Неуважительное отношение к другим пользователям и оскорбление — бан.\n-Разжигание межнациональной розни, конфликтов на политической и религиозном основании — бан.\n-Трансляция стримов — бан.\n-Администрация сервера оставляет за собой право редактировать правила иначе.```");
                $builder = MessageBuilder::new();      
                $builder->addEmbed($LogoEmbededMessage);
                $builder->addEmbed($PravEmbededMessage);      
                $message->channel->sendMessage($builder);
            };
            if (mb_strtolower($message->content) == '!emb2') {
                $LogoEmbededMessage = new Embed($discord);
                $LogoEmbededMessage->setColor('#946D13');
                $LogoEmbededMessage->setImage('https://cdn.discordapp.com/attachments/1049567414842572881/1049567466034036776/nav.png');
                $PravEmbededMessage = new Embed($discord);
                $PravEmbededMessage->setColor('#946D13');
                $PravEmbededMessage->setDescription("\n\n```                   🌐 ИНФОРМАЦИЯ:```\n`«Поддрержка»`\n\n<#971401999474118666> - узнать интересующие вас вопросы у <@&981030940065296416> или посмотреть часто задаваемые вопросы по серверу.\n<#971402066910150736> - крайний случай когда больше нельзя терпеть, призыв <@&964518359058235413> или посмотреть часто задаваемы вопросы админам.\n\n`«Основная информация»`\n\n<#964517710832734251>  - вход на сервер(приглашение).\n<#968184674121830510> - основные правила.\n<#968338767876333648>  - информация по серверу / получение необходимых ролей.\n<#984276281833164862> - главные новости сервера.\n\n`«Торгаш»`\n\n<#994260584944124084> - то с чем могу помочь(но не за просто так).\n<#994267193476915201> - вопросы по разделу выше.\n\n`«Для общения»`\n\n<#968539788665831424> - чат для общения.\n\n`«Сборка-ботов»`\n\n<#972169881732657202>  - комната для проверки бота.__(только для тестеров)__\n<#968540149250162749>  - чат приглашения бота для просмотра аниме.__(только для тестеров)__\n<#969271819670548580>  - посмотреть изминения в ботах.\n<#969274606676484149>  - предложить хорошую идею.\n<#978625379348664340> - ссылки для приглашение ботов на ваш сервер.__(только для тестеров)__\n\n`«Непристойный»`\n\n<#977619966041944134> - посмотреть/запросить(IMG) у бота NSFW контент(Команда: **!nsfwаним**).\n<#1014122517633441862> - посмотреть/запросить(IMG) у бота SFW контент(Команда: **!sfwаним**).\n<#1014122859720871976> - посмотреть/запросить(IMG) у бота NEKO контент(Команда: **!nekoаним**).\n<#1014122909129781338> - посмотреть/запросить(IMG) у бота TRAP контент(Команда: **!trapаним**).\n<#1014123019851022356> - посмотреть/запросить(GIF) у бота BLOWJOB контент(Команда: **!bljаним**).\n<#1014122591516110918> - посмотреть/запросить(MPEG-4) у бота Порно контент(Команда: **!porn**).\n\n`«Помощь PHPБОТ»`\n\n<#969271819670548580> - общая информация по обновлению ботов на сервере.\n<#969274606676484149> - ваши идеи по улучшению ботов(как тех кто на этом сервере. Так и идеи для ваших ботов).\n<#973576952864727131> - общение на темы связанные с ботами написанными на PHP.\n<#977450849511018556> - вопросы по созданию ботов на PHP(можно быстро получить ответ).\n<#1041341188457906196> - канал предназначен для тех кто уже начали или только собирается развивать свой аккаунт на codewars(если у вас вдруг западает решение какой то задачи, то вероятнее всего его решение можно будет найти здесь).\n\n`«Помощь HTML/CSS/JS/PHP/SQL»`\n\n<#1018545388816441406> - в основном сюда отправляется уже готовое решение вопроса или какие то интересные кусочки кода, которые можно будет применить в будуйщем.\n<#1018545825808392293> - обсуждение ваших вопросов и их решение.\n\n`«Помощь Arma3»`\n\n<#973578737960517642> - в основном готовые решения по скриптам arma3 + небольшие уроки(видео или ссылки на статьи).\n<#1016748899970584740> - свободное общение на тему arma3.\n\n`«Помощь игры»`\n\n<#1005116040616890520> - интересная информация для разработчиков игр и не только(стоит глянуть).\n\n```                   🥐 РОЛИ:```\n\n`Роли управления сервером`\n\n<@&964518359058235413> ➣ Создатель сервера(далёк от народа).\n<@&981030940065296416> ➣ Модератор сервера(именно они вам помогают).\n\n`Эксклюзивные роли`\n\n<@&1049680865799704618>  ➣ Бустеры сервера.\n\n`Роли для теста ботов/игр и т.п`\n\n<@&968198654978580550>  ➣ Выдаётся на время теста бота.\n\n`Роли разработчиков`\n\n<@&981024614366978148>  ➣ Вы знаете один из языков программирования с пониманием основ и занимаетесь разработкой ботов .\n#Скоро будет  ➣ Вы знаете такие языки как - C++ / C# / SQF / SQM / SQL / PHP / JS и т.д.\n<@&1005117361130590250>  ➣ Вы знаете один из языков программирования, занимались разработкой даже самых простых игр.\n\n`Основные роли`\n\n<@&981023817793159178>  ➣ При получение 70+ уровня.\n<@&981023397750390826>  ➣ При получение 65 уровня.\n<@&981022206433832970>  ➣ При получение 50 уровня.\n<@&981021926229164083>  ➣ При получение 35 уровня.\n<@&981020586740449360>  ➣ При получение 20 уровня.\n<@&981020163547738163>  ➣ При получение 5 уровня.\n<@&964518066832691230>  ➣ Выдаётся при входе на сервер.");
                $builder = MessageBuilder::new();
                $builder->addEmbed($LogoEmbededMessage);
                $builder->addEmbed($PravEmbededMessage);
                $message->channel->sendMessage($builder);
            };
        };
        if(($message->member->getPermissions()->administrator) == 1){
            if(mb_strtolower($message->content) == '!под'){
            $LogoEmbededMessage = new Embed($discord);
            $LogoEmbededMessage->setColor('#640c44');
            $LogoEmbededMessage->setImage('https://i.ibb.co/qWzG3y2/help-img.png');
            $TpEmbededMessage = new Embed($discord);
            $TpEmbededMessage->setColor('#640c44');
            $TpEmbededMessage->setDescription("```🛸 Непонятные RU |Поддержка``` \n **Появились проблемы? Хотите предложить идею для сервера? Мы вам поможем!** \n ```Чтобы связаться с поддержкой нашего сервера нажмите на кнопку```");
        
            $builder = MessageBuilder::new();
            $builder->addEmbed($LogoEmbededMessage);
            $builder->addEmbed($TpEmbededMessage);
        
            $actionRow = ActionRow::new();
        
            $helpuser = Button::new(Button::STYLE_SUCCESS);
            $helpuser->setLabel('🏷️ Создать тикет!');
            $helpuser->setCustomId("create_ticket");
        
            $helpinfo = Button::new(Button::STYLE_SUCCESS);
            $helpinfo->setLabel('🗂️Частые вопросы');
            $helpinfo->setCustomId("frequent_questions");
        
            $actionRow->addComponent($helpuser);
            $actionRow->addComponent($helpinfo);
        
            $builder->addComponent($actionRow);
        
            $message->channel->sendMessage($builder);
            };
            if(mb_strtolower($message->content) == '!адм'){
            $LogoEmbededMessage = new Embed($discord);
            $LogoEmbededMessage->setColor('#640c44');
            $LogoEmbededMessage->setImage('https://i.ibb.co/qWzG3y2/help-img.png');
            $TpEmbededMessage = new Embed($discord);
            $TpEmbededMessage->setColor('#640c44');
            $TpEmbededMessage->setDescription("```🛸 Непонятные RU |Поддержка``` \n **Появились проблемы в голосовом канале? Мы вам поможем!** \n ```Чтобы позвать поддержку нашего сервера в голосовой канал нажмите на кнопку```");
        
            $builder = MessageBuilder::new();
            $builder->addEmbed($LogoEmbededMessage);
            $builder->addEmbed($TpEmbededMessage);
        
            $actionRow = ActionRow::new();
        
            $helpusera = Button::new(Button::STYLE_SUCCESS);
            $helpusera->setLabel('👁️ Позвать админа!');
            $helpusera->setCustomId("help_adm");
        
        
            $helpinfo = Button::new(Button::STYLE_SUCCESS);
            $helpinfo->setLabel('🗂️Частые вопросы');
            $helpinfo->setCustomId("frequent_questions");
        
            $actionRow->addComponent($helpusera);
            $actionRow->addComponent($helpinfo);
        
            $builder->addComponent($actionRow);
        
            $message->channel->sendMessage($builder);
            };   
        };
    };
});

$discord->listenCommand('краснеть', function (Interaction $interaction) use ($discord, $browser) {
    $browser->get('https://api.waifu.pics/sfw/blush')->then(function (ResponseInterface $response) use ($discord, $interaction) {
        $blush = json_decode($response->getBody())->url;
        $interaction->respondWithMessage(MessageBuilder::new()->setContent($blush));
    });
});

$discord->listenCommand('чакцитата', function (Interaction $interaction) use ($discord, $browser) {
    $browser->get('https://api.chucknorris.io/jokes/random')->then(function (ResponseInterface $response) use ($discord, $interaction) {
        $infojoke = json_decode($response->getBody());
        $interaction->respondWithMessage(MessageBuilder::new()->setContent($infojoke->value));
    });
});

$discord->listenCommand('анимецитата', function (Interaction $interaction) use ($discord, $browser) {
    $browser->get('https://animechan.vercel.app/api/random')->then(function (ResponseInterface $response) use ($discord, $interaction) {
        $animemove = json_decode($response->getBody());
        $anim = $animemove->anime . ' - ' . $animemove->quote;
        $interaction->respondWithMessage(MessageBuilder::new()->setContent($anim));
    });
});

$discord->listenCommand('пидор', function (Interaction $interaction) use ($discord) {
    $idname = $interaction->member->id;
    if ($idname == "409998159218081792") {
        $procpid = rand(0, 10);
        $pidor = "Батюшка вы на $procpid% пидор!\nТы же мой отец(ты в априори пидор!)";
        $interaction->respondWithMessage(MessageBuilder::new()->setContent($pidor));
    } else {
        $procpid = rand(0, 100);
        if ($procpid > '50') {
            $EmbededMessage = new Embed($discord);
            $EmbededMessage->setColor('#079dbb');
            $EmbededMessage->setDescription("Кажется вы пидор (на {$procpid}%)!");
            $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
        } else {
            if ($procpid < '10') {
                $EmbededMessage = new Embed($discord);
                $EmbededMessage->setColor('#e94dfe');
                $EmbededMessage->setDescription("Да вы святой, вам не надо знать свои проценты! (скажу по секрету: {$procpid}%)");
                $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
            } else {
                $EmbededMessage = new Embed($discord);
                $EmbededMessage->setColor('#504dfe');
                $EmbededMessage->setDescription("Вы на {$procpid}% пидор!");
                $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
            }
        };
    };
});

$discord->listenCommand('удар', function (Interaction $interaction) use ($discord, $browser) {
    $userid = $interaction->data->options['участник']->value;
    $guild = $interaction->guild_id;
    $browser->get('https://api.waifu.pics/sfw/bonk')->then(function (ResponseInterface $response) use ($discord, $interaction, $userid, $guild) {
        $bonk = json_decode($response->getBody())->url;
        $guild = $discord->guilds->get('id', $guild);
        $member = $guild->members;
        $targetname = $member[$userid]->username;
        $EmbededMessage = new Embed($discord);
        $EmbededMessage->setColor('#fcfcfc');
        $EmbededMessage->setImage($bonk);
        $EmbededMessage->setDescription("**{$targetname}**, вам просили передать:");
        $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
    });
});

$discord->listenCommand('ответ', function (Interaction $interaction) use ($discord) {
    $procpid = rand(1, 2);
    if ($procpid % 2 == 0) {
        $TrueEmbededMessage = new Embed($discord);
        $TrueEmbededMessage->setColor('#59fe4d');
        $TrueEmbededMessage->setDescription("Мой ответ - да");
        $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($TrueEmbededMessage));
    } else {
        $FalseEmbededMessage = new Embed($discord);
        $FalseEmbededMessage->setColor('#fe4d4d');
        $FalseEmbededMessage->setDescription("Мой ответ - нет");
        $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($FalseEmbededMessage));
    };
});

$discord->listenCommand('кубик', function (Interaction $interaction) use ($discord) {
    $cub = rand(1, 6);
    $kubebmed = new Embed($discord);
    $kubebmed->setColor("#272626");
    $kubebmed->setDescription("Выпало число **{$cub}**");
    $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($kubebmed));
});

$discord->listenCommand('см', function (Interaction $interaction) use ($discord) {
    $sm = rand(-10, 35);
    if ($sm < '0') {
        $EmbededMessage = new Embed($discord);
        $EmbededMessage->setColor('#9b02ed');
        $EmbededMessage->setDescription("Похоже вы __женщина__ - у вас **{$sm}см**");
        $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
    } else {
        if ($sm > '30') {
            $EmbededMessage = new Embed($discord);
            $EmbededMessage->setColor('#0231ed');
            $EmbededMessage->setDescription("У тебя что то болтается внизу, подбери **({$sm}см)**");
            $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
        } else {
            if ($sm > '15') {
                $EmbededMessage = new Embed($discord);
                $EmbededMessage->setColor('#079dbb');
                $EmbededMessage->setDescription("Среднестатистические **{$sm}см**");
                $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
            } else {
                $EmbededMessage = new Embed($discord);
                $EmbededMessage->setColor('#7e7902');
                $EmbededMessage->setDescription("Сходи к доктору **{$sm}см**");
                $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($EmbededMessage));
            };
        };
    };
});

$discord->listenCommand('сексуальный', function (Interaction $interaction) use ($discord) {
    $sexsi = rand(0, 101);
    $sexebmed = new Embed($discord);
    $sexebmed->setColor("#fcfcfc");
    $sexebmed->setDescription("Ваша сексуальность составляет - **{$sexsi}%**");
    $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($sexebmed));
});

$discord->listenCommand('помощь', function (Interaction $interaction) use ($discord) {
    $guild = $interaction->guild_id;
    $guild = $discord->guilds->get('id', $guild)->name;
    echo $guild . PHP_EOL;
    $helpebmed = new Embed($discord);
    $helpebmed->setColor("#fcfcfc");
    $helpebmed->setTimestamp();
    $helpebmed->setDescription("**{$guild}**\n\n**📷Картинки📷**\n | Посмотреть на аниме вайфу 18+ - `!nsfwаним`\n | Посмотреть на девушка-кошка 18+ - `!nekoаним`\n | Посмотреть на трап 18+ - `!trapаним`\n | Посмотреть на аниме вайфу 16+ - `!sfwаним`\n\n**📹Гифки📹**\n | Посмотреть на минет 18+ - `!bljаним`\n | Милая гифка краснеющих аниме девочке - `/краснеть`\n\n**📑Цитаты📑**\n | Почитать цитаты Чак Нориса - `/чакцитата`\n | Почитать аниме цитаты - `/анимецитата`\n\n **🔦Узнать о себе**\n | Посчитать на сколько % ты пидор - `/пидор`\n | Узнать свою сексуальность - `/сексуальный`\n | Рассказать всем сколько у тебя см - `/см`\n | Получить ответ на свой вопрос **да** или **нет** - `/ответ`\n\n **🪝Оказать воздействие на участника сервера🪝**\n | Ударить участника сервера - `/удар [:участник]`\n\n**🎮Игры🎮**\n | Бросить кубик - `/кубик`\n\n **💫Универсальные💫**\n | Узнать свой уровень - `/уровень`\n");
    $interaction->respondWithMessage(MessageBuilder::new()->addEmbed($helpebmed));
});

$discord->run();
