<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\Message\Message;
use Gomoob\WebSocket\Request\WebSocketRequest;

use PHPUnit\Framework\TestCase;

/**
 * Test case used to test the `\Gomoob\WebSocket\Client\WebSocketClient` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 * @group WebSocketClientTest
 */
class WebSocketClientTest extends TestCase
{
    /**
     * Test method for `send(IWebSocketRequest $webSocketRequest)`.
     */
    public function testSend()
    {
        $webSocketClient = new WebSocketClient('ws://localhost:8080');
        
        // Backup the Textalk WebSocket client
        $reflectionProperty = new \ReflectionProperty($webSocketClient, 'textalkWebSocketClient');
        $reflectionProperty->setAccessible(true);
        $textalkWebSocketClient = $reflectionProperty->getValue($webSocketClient);
        $textalkWebSocketClientMock = $this->createMock(\WebSocket\Client::class);
        $reflectionProperty->setValue($webSocketClient, $textalkWebSocketClientMock);
        
        $textalkWebSocketClientMock->expects($this->exactly(4))->method('send')->withConsecutive(
            [
                $this->equalTo(
                    '{'.
                        '"message":{"metadata":[]},'.
                        '"tags":[]' .
                    '}'
                )
            ],
            [
                $this->equalTo(
                    '{'.
                        '"message":{"type":"MY_TYPE",' .
                        '"metadata":{"prop1":"prop1Value","prop2":"prop2Value"}},' .
                        '"tags":[]' .
                    '}'
                )
            ],
            [
                $this->equalTo(
                    '{' .
                        '"message":{"type":"MY_TYPE","metadata":[]},' .
                        '"tags":{"tag1":"tag1Value","tag2":"tag2Value"}' .
                    '}'
                )
            ],
            [
                $this->equalTo('{"message":{"metadata":[]},"tags":[],"metadata":{"token":"A_TOKEN"}}')
            ]
        );
        
        // Test with no tags and an empty message
        $webSocketClient->send(WebSocketRequest::create(Message::create()));
        
        // Test with no tags and a message with type and metadata
        $webSocketClient->send(
            WebSocketRequest::create(Message::create('MY_TYPE', ['prop1'=>'prop1Value','prop2'=>'prop2Value']))
        );
        
        // Test with tags and a message with type and metadata
        $webSocketClient->send(
            WebSocketRequest::create(Message::create('MY_TYPE'), ['tag1' => 'tag1Value', 'tag2' => 'tag2Value'])
        );
        
        // Test with additional metadata
        $webSocketClient->send(
            WebSocketRequest::create(Message::create())->setMetadata(['token' => 'A_TOKEN'])
        );

        $reflectionProperty->setValue($webSocketClient, $textalkWebSocketClient);
    }
}
