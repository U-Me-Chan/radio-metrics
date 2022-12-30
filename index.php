<?php

require_once __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Loop;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ridouchire\RadioMetrics\DataCollector;

$current_data = [];

$handler = new StreamHandler(__DIR__ . '/general.log', Level::Debug);

$logger = new Logger('metrics');
$logger->pushHandler($handler);

$logger->info('Старт демона...');

Loop::addPeriodicTimer(1, function () use ($logger, &$current_data) {
    $collector = new DataCollector();

    $logger->debug('Запрос данных...');

    $data = $collector->getData();

    if (isset($data['error'])) {
        $logger->error("Ошибка запроса данных", $data);
    }

    if (!empty(array_diff($data, $current_data))) {
        $current_data = $data;

        $logger->debug("Данные изменились, обновляю лог");
        $logger->info("data", $current_data);
    } else {
        $logger->debug("Данные не менялись");
    }
});
