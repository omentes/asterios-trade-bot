<?php

require __DIR__ . '/vendor/autoload.php';
$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();
$commands_paths = [
    __DIR__ . '/App/Commands/',
];

$token  = getenv('TG_TOKEN');
$botName  = getenv('TG_BOT_NAME');
$mysql_credentials = [
    'host'     => getenv('DB_HOST'),
    'port'     => getenv('DB_PORT'),
    'user'     => getenv('DB_USERNAME'),
    'password' => getenv('DB_PASSWORD'),
    'database' => getenv('DB_NAME'),
];

try {
    $logger = new Monolog\Logger('asterios-bot');
    $logger->pushHandler(new  Monolog\Handler\StreamHandler(__DIR__.'/logs/app.log'));
    $telegram = new Longman\TelegramBot\Telegram($token, $botName);
    $telegram->addCommandsPaths($commands_paths);
    $redis = new Predis\Client();
//    $hookRegistrator = new App\BotRegistrator($telegram, $redis, $logger);
    Longman\TelegramBot\TelegramLog::initialize($logger);
//    $hookRegistrator->register();
    $telegram->enableMySql($mysql_credentials);
    $telegram->enableLimiter();
    $telegram->handleGetUpdates();

}  catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    // Catch log initialisation errors
    $logger->error($e->getMessage());
} catch (Exception $e) {
    $logger->error($e->getMessage());
}


//echo json_encode(['done' => true]);
