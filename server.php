<?php

use Gomoob\WebSocket\Server\WebSocketServer;

require __DIR__ . '/vendor/autoload.php';

echo "WebSocket server started, enter Ctrl+C to stop server." . PHP_EOL . PHP_EOL;
WebSocketServer::factory()->run();
