<?php

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\SendGridHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Logger;

require __DIR__."/vendor/autoload.php";

$logger = new Logger("web");

/**
 * Niveís de logs:
 * EMERGENCY
 * ALERT
 * CRITICAL
 * ERROR
 * WARNING
 * NOTICE
 * INFO
 * DEBUG
 */

// Irá mostrar todos os logs no console à partir de DEBUG
$logger->pushHandler(new BrowserConsoleHandler(Logger::DEBUG));

// Irá salvar todos os logs em arquivos à partir de WARNING
$logger->pushHandler(new StreamHandler(__DIR__."/log.txt", Logger::WARNING));

// Irá enviar por e-mail todos os logs à partir de CRITICAL
$logger->pushHandler(new SendGridHandler(
    SENDGRID["user"],
    SENDGRID["api_key"],
    "lemayara16@gmail.com",
    "mayara-kit@hotmail.com",
    "Teste",
    Logger::CRITICAL
));

$tele_key = "1514434940:AAEfIJQIpwl0Yjam_E0azQEREHyAywCH2io";
$tele_channel = "-1001282862348";
$tele_handler = new TelegramBotHandler(
    $tele_key,
    $tele_channel,
    Logger::EMERGENCY
);
$tele_handler->setFormatter(new LineFormatter("%level_name%: %message%"));

// Irá enviar uma mensagem pelo Telegram quando for EMERGENCY
$logger->pushHandler($tele_handler);

// Último parâmetro do log
$logger->pushProcessor(function ($record) {
    $record["extra"]["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
    $record["extra"]["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
    $record["extra"]["REQUEST_METHOD"] = $_SERVER["REQUEST_METHOD"];
    $record["extra"]["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
    return $record;
});

// Console
$logger->debug("Olá Mundo!", ["logger" => true]);
$logger->info("Olá Mundo!", ["logger" => true]);
$logger->notice("Olá Mundo!", ["logger" => true]);

// Console + Arquivo
$logger->warning("Olá Mundo!", ["logger" => true]);
$logger->error("Olá Mundo!", ["logger" => true]);

// Console + Arquivo + E-mail
$logger->critical("Olá Mundo!", ["logger" => true]);
$logger->alert("Olá Mundo!", ["logger" => true]);

// Console + Arquivo + E-mail + Telegran
$logger->emergency("Essa mensagem foi enviada pelo Monolog!");
