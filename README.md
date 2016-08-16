# Gomoob WebSocket server

> WebSocket server with tags management, forward your messages on the right clients with ease !

# Introduction

The Gomoob WebSocket server is a simple Ratchet server which works with custom tags to easily forward the right messages 
to the right clients.

As an example let's suppose we have a Web Application with English and French users. English users should receive 
English messages, French users should receive French messages.

Each application opens a Web Socket with a particular `language` tag.

```javascript
// Web Application in English mode
var enWebSocket = new WebSocket('ws://localhost:8080?tags={"language":"EN}');

...

// Web Application in French mode
var frWebSocket = new WebSocket('ws://localhost:8080?tags={"language":"FR}');

...
```

On server side the Gomoob WebSocket server keeps track of the associations between tags and WebSocket connections. For 
example this simple PHP peace of code allows to easily forward a message to all clients connected with the `language=FR` tag.

```php
// PHP Server (in most cases a Web Server) to Web Socket server client, allows to send one message which is forwared to
// several opened WebSocket connections
$phpClient = new WebSocketClient('ws://localhost:8080');
$phpClient->send(WebSocketRequest::create($message, ['language' => 'FR']);
```

# Installation

## Server side (run the server)

Running a server requires only one line of code.

```php
require __DIR__ . '/vendor/autoload.php';

echo "WebSocket server started, enter Ctrl+C to stop server." . PHP_EOL;
\Gomoob\WebSocket\Server\WebSocketServer::factory()->run();
```

## Client side (PHP)

First pull the project with composer using the following dependency.

```json
{
    "require": {
        "gomoob/php-websocket-server": "^1.0.0"
    }
}
```

Then simply use the `\Gomoob\WebSocket\Client\WebSocketClient` class to send your messages.

```php
// Open a Server / Server WebSocket connection
$phpClient = new WebSocketClient('ws://localhost:8080');

// Forward a message to all the WebSocket client connections associated to 'tag1' and 'tag2'
$response = $phpClient->send(
    WebSocketRequest::create(
        $message, 
        [
            'tag1' => 'tag1Value',
            'tag2' => 'tag2Value'
        ]
    )
);
```

# Release history

## 0.1.0 (2016-08-17)
 * First release.
