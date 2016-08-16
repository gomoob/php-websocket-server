<?php

use Gomoob\WebSocket\Server\WebSocketServer;
use Gomoob\WebSocket\Message\MessageParser;

require __DIR__ . '/../../vendor/autoload.php';

echo "WebSocket server started, enter Ctrl+C to stop server." . PHP_EOL . PHP_EOL;
WebSocketServer::factory(
    [
    	'port' => 10000,
    	'messageParser' => new MessageParser()
    ]
)->run();
