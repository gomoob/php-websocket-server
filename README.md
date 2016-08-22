# Gomoob WebSocket server

> WebSocket server with tags management, forward messages on the right clients with ease !

[![Total Downloads](https://img.shields.io/packagist/dt/gomoob/php-websocket-server.svg?style=flat)](https://packagist.org/packages/gomoob/php-websocket-server) 
[![Latest Stable Version](https://img.shields.io/packagist/v/gomoob/php-websocket-server.svg?style=flat)](https://packagist.org/packages/gomoob/php-websocket-server) 
[![Build Status](https://img.shields.io/travis/gomoob/php-websocket-server.svg?style=flat)](https://travis-ci.org/gomoob/php-websocket-server)
[![Coverage](https://img.shields.io/coveralls/gomoob/php-websocket-server.svg?style=flat)](https://coveralls.io/r/gomoob/php-websocket-server?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/gomoob/php-websocket-server.svg?style=flat)](https://codeclimate.com/github/gomoob/php-websocket-server)
[![License](https://img.shields.io/packagist/l/gomoob/php-websocket-server.svg?style=flat)](https://packagist.org/packages/gomoob/php-websocket-server)

## Introduction

The Gomoob WebSocket server is a simple [Ratchet](http://socketo.me "Ratchet") server which works with custom tags 
to easily forward messages to clients depending on custom tags.

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

## Installation

### Server side (run the server)

Running a server requires only one line of code.

```php
require __DIR__ . '/vendor/autoload.php';

echo "WebSocket server started, enter Ctrl+C to stop server." . PHP_EOL;
\Gomoob\WebSocket\Server\WebSocketServer::factory()->run();
```

### Client side (PHP)

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

If you want to write solid unit test we also provide the `\Gomoob\WebSocket\Client\WebSocketClientMock` 
class. This class is a utility mock which is very easy to use.

```php
// Somewhere in our code we use a \Gomoob\WebSocket\IWebSocketClient ...
// We suppose this code is implemented in MyPowerfulService->serviceMethod();
$phpClient->send(WebSocketRequest::create('Message 0.')->setTags(['tag0' => 'tag0Value']));
$phpClient->send(WebSocketRequest::create('Message 1.')->setTags(['tag1' => 'tag0Value']));
$phpClient->send(WebSocketRequest::create('Message 2.')->setTags(['tag0' => 'tag0Value', 'tag1' => 'tag1Value']));
		
// Then we write a test case by replacing the real WebSocket client implementation with the mock one
class SampleTestCase extends TestCase
{
     public function setUp() {
        $this->webSocketClient = new WebSocketClientMock();
        $this->myPowerfulService->setWebSocketClient($this->webSocketClient);
     }

    public function testServiceMethod() {
    
        // Calls the method to test
        $this->myPowerfulService->serviceMethod();
    
        // Checks the right requests were sent
        $webSocketRequests = $this->webSocketClient->findByTags(['tag0' => 'tag0Value']);
		  $this->assertCount(2, $webSocketRequests);
		  $this->assertContains($webSocketRequest0, $webSocketRequests);
		  $this->assertNotContains($webSocketRequest1, $webSocketRequests);
		  $this->assertContains($webSocketRequest2, $webSocketRequests);
    }
}
```

## Advanced configuration

The default behavior of the Gomoob WebSocket server is the following !

 * Expose port `8080` and authorize connections from all IP addresses (i.e `0.0.0.0`) ; 
 * Accept only plain string messages (exceptions are encountered if JSON messages are sent / received) ;
 * Use a default PSR logger which output messages on the terminal ; 
 * Do not manage any authorization checks.

If one of those behaviors does not fit your need please read the following sub sections. You can also read the `src/test/server.php` file which shows how to start a server with custom message parsing and authorizations.

### Message parser

By default the WebSocket server will accept plain string messages, if you try to send a JSON object then you'll 
encounter the following exception. 

```
The 'message' property is not a string, you must configure a message parser to parse messages !
```

This is the expected behavior, if you want the server to manage custom PHP object messages then you have to :

 * Make your PHP object messages extends `\JsonSerializable` and implement the `jsonSerialize()` method 
   correctly ; 
 * Implement a custom message parser to create your custom PHP object messages when a plain JSON object is received.

A sample message object is provided in the `\Gomoob\WebSocket\Message\Message` class, feel free to read the 
associated source code to understand how it works. You'll also found a sample message parser in the `\Gomoob\WebSocket\Message\MessageParser`. 

To explain how to manage custom PHP object messages let's suppose we have the following message object to send. 

```php
class MyMessage {
    private $messageProperty1;
    public function __construct($messageProperty1) {
        $this->messageProperty1 = $messageProperty1; 
    }
    public function getMessageProperty1() {
        return $this->messageProperty1;
    }
}
```

Sending such a message in a browser on Javascript would require the following code.

```javascript
var socket = new WebSocket('ws://localhost:8080');
socket.send(
    JSON.stringify(
        {
            message : {
                messageProperty1 : "Hello !"
            },
            tags : {
                tag1 : 'tag1Value'
            }
        }
    )
);
```

Or in PHP with the client we provide.

```php
WebSocketClient::factory('ws://localhost:8080')->send(WebSocketRequest::create(new MyMessage('Hello !')));
```

As this this will not work because on server side the Gomoob WebSocket server will not know how to parse the messages 
and how to re-create those messages to forward them to clients who opened WebSocket connections.

The first thing to do is to implement the `\JsonSerializable` class and the `jsonSerializeMethod()` in our 
`MyMessage` class.

```php
class MyMessage implements \JsonSerializable {
   ...
   public function jsonSerialize() {
       return [
           'messageProperty1' => $this->messageProperty1;
        ];
   }
}
```

Then we have to implement a message parser by extending the `\Gomoob\WebSocket\IMessageParser` class.

```php
use Gomoob\WebSocket\IMessageParser;

class MyMessageParser implement IMessageParser {
    public function parse(array $arrayMessage)
    {
        // Ensure the array contains only valid key names
        foreach (array_keys($arrayMessage) as $key) {
            if (!is_string($key) || !in_array($key, ['messageProperty1'])) {
                throw new \InvalidArgumentException('Unexpected property \'' . $key . '\' !');
            }
        }
        
        // The 'messageProperty1' property is mandatory
        if (!array_key_exists('messageProperty1', $arrayMessage)) {
            throw new \InvalidArgumentException('No \'messageProperty1\' property found !');
        }
        
        return new MyMessage($arrayMessage['messageProperty1']);
    }
}
```

Finally we have to provide our parser when we create our WebSocket server.

```php
WebSocketServer::factory(
    [
    	'messageParser' => new MyMessageParser()
    ]
)->run();
```

### Authorization Manager

By default the WebSocket server will accept all connections and message sendings, in most cases this behavior is not 
expected because anybody could open a WebSocket on your server and try to forward messages to all connected clients 
without authorization.

You can implement a custom authorization manager by implementing the `\Gomoob\WebSocket\IAuthManager` 
interface, this interface has the following signature.

```php
/**
 * Interface which defines an authorization manager. An authorization manager allows to control authorization while
 * opening Web Socket connections and sending messages over Web Sockets.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 */
interface IAuthManager
{
    /**
     * Function used to indicate if connection opening is authorized.
     *
     * @param \Ratchet\ConnectionInterface $connection the current Ratchet connection.
     *
     * @return boolean `true` if the connection opening is authorized, `false` otherwise.
     */
    public function authorizeOpen(ConnectionInterface $connection);

    /**
     * Function used to indicate if message sending is authorized.
     *
     * @param \Ratchet\ConnectionInterface $connection the current Ratchet connection.
     * @param \Gomoob\WebSocket\IWebSocketRequest $webSocketRequest the current Gomoob WebSocket request.
     */
    public function authorizeSend(ConnectionInterface $connection, IWebSocketRequest $webSocketRequest);
}
```

So its very easy to manage authorizations, just return `true` or `false` with the `authorizeOpen(...)` or 
`authorizeSend(...)` functions.

#### The `ApplicationsAuthManager`

To easier authorization we provide an authorization manager which allows to declare several applications with `key` 
and `secret` properties.

This authorization manager is available in the `\Gomoob\WebSocket\Auth\ApplicationsAuthManager` class, it 
works with a very simple YAML configuration file.

Here is a sample instanciation of the manager with a WebSocket server.

```php
WebSocketServer::factory(
    [
    	'authManager' => ApplicationsAuthManager::factory(
    	    [
    	        'authorizeOpen' => false,
    	    	'configurationFile' => __DIR__ . '/auth.yml'
    	    ]
    	)
    ]
)->run();
```

The content of the `auth.yml` file could be the following.

```yaml
applications:
  - 
    key: application1
    secret: B4ajW3P7jfWEYPZsQV8mnteHg97G67uW
    authorizeOpen: true
  - key: application2
    secret: 33yLWdynhaqm9tYjDFKf8gB8zmAPKdDP
    authorizeOpen: false
```

Then the followig Javascript peace of code will apply.

```javascript
// Does not work because required 'key' and 'secret' URL parameters are not provided
var socket1 = new WebSocket('wss://myserver.org:8080'); 

// Works because the 'key' and 'secret' URL parameters provided are valid
var socket2 = new WebSocket('wss://myserver.ord:8080?key=application1&secret=B4ajW3P7jfWEYPZsQV8mnteHg97G67uW');

// Does not work because the request does not provide the 'key' and 'secret' properties
socket2.send(
    JSON.stringify(
        {
            message : {
                messageProperty1 : "Hello !"
            }
        }
    )
);

// Works because the request provides valid 'key' and 'secret' properties
socket2.send(
    JSON.stringify(
        {
            message : {
                messageProperty1 : "Hello !"
            },
            metadata : {
                key : 'application2',
                secret : '33yLWdynhaqm9tYjDFKf8gB8zmAPKdDP'
            }
        }
    )
);
```

The same rules are also applicable with the PHP client we provide.

```php
WebSocketClient::factory('ws://localhost:8080')->send(
    WebSocketRequest::create(
        new MyMessage('Hello !')
    )->setMetadata(
        [
            'key' => 'application2',
            'secret' => '33yLWdynhaqm9tYjDFKf8gB8zmAPKdDP'
        ]
    )
);
```

## Docker container

To help you start quickly we also provide a Docker container here https://hub.docker.com/r/gomoob/php-websocket-server.

## Release history

### 1.2.0 (2016-08-23)
 * Moves the `TagsTree` class to `\Gomoob\WebSocket\Util\TagsTree` ; 
 * Add a new `TagsTree->reset()` method ; 
 * Add a new `\Gomoob\WebSocket\Client\WebSocketClientMock` class to easier unit testing ;
 * Update composer dependencies.

### 1.1.0 (2016-08-18)
 * Add more PHP Documentor documentation about the goals of `metadata` in 
   the `\Gomoob\WebSocket\IWebSocketRequest` interface and the 
   `\Gomoob\WebSocket\Request\WebSocketRequest` class ; 
 * Add management of `defaultMetadata` in the `\Gomoob\WebSoscket\IWebSocketClient` interface and the 
   `\Gomoob\WebSocket\Client\WebSocketClient` class ;
 * Add management of `defaultTags` in the `\Gomoob\WebSocket\IWebSocketClient` interface and the 
   `\Gomoob\WebSocket\Client\WebSocketClient` class ; 
 * Improve `\Gomoob\WebSocket\Message\Message` serialization ;
 * Improve `\Gomoob\WebSocket\Request\WebSocketRequest` serialization ;
 * Now all the factory methods can be calls with a `factory(...)` method or an alias `create(...)` method.

### 1.0.3 (2016-08-17)
 * Fix `port` and `address` options problems while creating a `WebSocketServer`, the parameter were not 
   transmitted to the Ratchet server ;
 * Now the default port number is `80` which is the default Ratchet server port.

### 1.0.2 (2016-08-17)
 * Add missing `symfony/yaml` composer dependency, otherwise problems was encountered while running 
   `composer update --no-dev` ;
 * Add missing `monolog/monolog` composer dependency, , otherwise problems was encountered while running 
   `composer update --no-dev`.

### 1.0.1 (2016-08-17)
 * Configure specific Eclipse validator rules ;
 * Add MIT license.

### 1.0.0 (2016-08-17)
 * First release.

## About Gomoob

At [Gomoob](https://www.gomoob.com) we build high quality software with awesome Open Source frameworks everyday. Would 
you like to start your next project with us? That's great! Give us a call or send us an email and we will get back to 
you as soon as possible !

You can contact us by email at [contact@gomoob.com](mailto:contact@gomoob.com) or by phone number 
[(+33) 6 85 12 81 26](tel:+33685128126) or [(+33) 6 28 35 04 49](tel:+33685128126).

Visit also http://gomoob.github.io to discover more Open Source softwares we develop.
