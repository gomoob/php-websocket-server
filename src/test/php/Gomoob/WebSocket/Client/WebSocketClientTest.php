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
        $reflectionProperty = new \ReflectionProperty($webSocketClient, 'client');
        $reflectionProperty->setAccessible(true);
        $client = $reflectionProperty->getValue($webSocketClient);
        $clientMock = $this->createMock(\WebSocket\Client::class);
        $reflectionProperty->setValue($webSocketClient, $clientMock);
        
        $clientMock->expects($this->exactly(6))->method('send')->withConsecutive(
            [
                // Test 1
                //  - no tags
                //  - empty message
                $this->equalTo(
                    '{'.
                        '"message":{"metadata":{}},'.
                        '"metadata":{},' .
                        '"tags":{}' .
                    '}'
                )
            ],
            [
                // Test 2
                //  - no tags
                //  - a message with type
                //  - metadata on request
                $this->equalTo(
                    '{'.
                        '"message":{"type":"MY_TYPE","metadata":{}},' .
                        '"metadata":{"prop1":"prop1Value","prop2":"prop2Value"},' .
                        '"tags":{}' .
                    '}'
                )
            ],
            [
                // Test 3
                //  - tags
                //  - a message with type
                //  - no metadata on request
                $this->equalTo(
                    '{' .
                        '"message":{"type":"MY_TYPE","metadata":{}},' .
                        '"metadata":{},' .
                        '"tags":{"tag1":"tag1Value","tag2":"tag2Value"}' .
                    '}'
                )
            ],
            [
                // Test 4
                //  - no tags
                //  - a message
                //  - metadata on request
                $this->equalTo(
                    '{' .
                        '"message":{"metadata":{}},' .
                        '"metadata":{"token":"A_TOKEN"},' .
                        '"tags":{}' .
                    '}'
                )
            ],
            [
                // Test 5
                //  - no tags
                //  - a message
                //  - metadata on request
                //  - default metadata on client
                $this->equalTo(
                    '{' .
                        '"message":{"metadata":{}},' .
                        '"metadata":{"met1":"met1ValueClient","met2":"met2ValueRequest","met3":"met3ValueClient"},' .
                        '"tags":{}' .
                    '}'
                )
            ],
            [
                // Test 6
                //  - no tags
                //  - a message
                //  - metadata on request
                //  - default metadata on client
                $this->equalTo(
                    '{' .
                        '"message":{"metadata":{}},' .
                        '"metadata":{"met1":"met1ValueClient","met2":"met2ValueRequest","met3":"met3ValueClient"},' .
                        '"tags":{"tag1":"tag1ValueClient","tag2":"tag2ValueClient","tag3":"tag3ValueRequest"}' .
                    '}'
                )
            ]
        );

        // Test 1
        //  - no tags
        //  - empty message
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create())
        );

        // Test 2
        //  - no tags
        //  - a message with type
        //  - metadata on request
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create('MY_TYPE'))
                ->setMetadata(['prop1'=>'prop1Value','prop2'=>'prop2Value'])
        );
        
        // Test 3
        //  - tags
        //  - a message with type
        //  - no metadata on request
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create('MY_TYPE'))
                ->setTags(['tag1' => 'tag1Value', 'tag2' => 'tag2Value'])
        );
        
        // Test 4
        //  - no tags
        //  - a message
        //  - metadata on request
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create())
                ->setMetadata(['token' => 'A_TOKEN'])
        );

        // Test 5
        //  - no tags
        //  - a message
        //  - metadata on request
        //  - default metadata on client
        $webSocketClient->setDefaultMetadata(
            [
                'met1' => 'met1ValueClient',
                'met2' => 'met2ValueClient',
                'met3' => 'met3ValueClient'
            ]
        );
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create())
                ->setMetadata(['met2' => 'met2ValueRequest'])
        );
        
        // Test 6
        //  - a message
        //  - metadata on request
        //  - tags on request
        //  - default metadata on client
        //  - default tags on client
        $webSocketClient->setDefaultMetadata(
            [
                'met1' => 'met1ValueClient',
                'met2' => 'met2ValueClient',
                'met3' => 'met3ValueClient'
            ]
        );
        $webSocketClient->setDefaultTags(
            [
                'tag1' => 'tag1ValueClient',
                'tag2' => 'tag2ValueClient',
                'tag3' => 'tag3ValueClient'
            ]
        );
        $webSocketClient->send(
            WebSocketRequest::factory(Message::create())
                ->setMetadata(['met2' => 'met2ValueRequest'])
                ->setTags(['tag3' => 'tag3ValueRequest'])
        );

        $reflectionProperty->setValue($webSocketClient, $client);
    }
}
