<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Request;

use Gomoob\WebSocket\Request\WebSocketRequest;

use PHPUnit\Framework\TestCase;
use Gomoob\WebSocket\Message\MessageParser;

/**
 * Test case used to test the `\Gomoob\WebSocket\Request\WebSocketRequest` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 * @group WebSocketRequestTest
 */
class WebSocketRequestTest extends TestCase
{
    /**
     * Test method for `createFromArray(array $array)`.
     */
    public function testCreateFromArray()
    {
        $messageParser = new MessageParser();
        
        // Test with bad keys
        try {
            WebSocketRequest::createFromArray(['badKey' => 'value'], $messageParser);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('Unexpected property \'badKey\' !', $iaex->getMessage());
        }
        
        // Test with no 'message' property provided
        try {
            WebSocketRequest::createFromArray(
                [
                    'tags' => [
                        'tag1' => 'tag1Value',
                        'tag2' => 'tag2Value'
                    ],
                    'metadata' => [
                        'met1' => 'met1Value',
                        'met2' => 'met2Value'
                    ]
                ],
                $messageParser
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('The \'message\' property is mandatory !', $iaex->getMessage());
        }
        
        // Test with an invalid 'message' property
        try {
            WebSocketRequest::createFromArray(
                [
                    'message' => [
                        'a' => 'b'
                    ],
                    'tags' => [
                        'tag1' => 'tag1Value',
                        'tag2' => 'tag2Value'
                    ],
                    'metadata' => [
                        'met1' => 'met1Value',
                        'met2' => 'met2Value'
                    ]
                ],
                $messageParser
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('Invalid \'message\' property !', $iaex->getMessage());
            $this->assertNotNull($iaex->getPrevious());
            $this->assertSame('Unexpected property \'a\' !', $iaex->getPrevious()->getMessage());
        }
        
        // Test with an invalid 'tags property
        try {
            WebSocketRequest::createFromArray(
                [
                    'message' => [
                        'type' => 'MY_TYPE',
                        'metadata' => [
                            'prop1' => 'prop1Value',
                            'prop2' => 'prop2Value'
                        ]
                    ],
                    'tags' => ['a' => ['bad']],
                    'metadata' => [
                        'met1' => 'met1Value',
                        'met2' => 'met2Value'
                    ]
                ],
                $messageParser
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame(
                'The tag named \'a\' has a value which is not an integer or a string !',
                $iaex->getMessage()
            );
        }
        
        // Test with a valid array
        $webSocketRequest = WebSocketRequest::createFromArray(
            [
                'message' => [
                    'type' => 'MY_TYPE',
                    'metadata' => [
                        'prop1' => 'prop1Value',
                        'prop2' => 'prop2Value'
                    ]
                ],
                'tags' => [
                    'tag1' => 'tag1Value',
                    'tag2' => 'tag2Value'
                ],
                'metadata' => [
                    'met1' => 'met1Value',
                    'met2' => 'met2Value'
                ]
            ],
            $messageParser
        );
        
        $this->assertSame('MY_TYPE', $webSocketRequest->getMessage()->getType());
        $this->assertCount(2, $webSocketRequest->getMessage()->getMetadata());
        $this->assertArrayHasKey('prop1', $webSocketRequest->getMessage()->getMetadata());
        $this->assertArrayHasKey('prop2', $webSocketRequest->getMessage()->getMetadata());
        $this->assertSame('prop1Value', $webSocketRequest->getMessage()->getMetadata()['prop1']);
        $this->assertSame('prop2Value', $webSocketRequest->getMessage()->getMetadata()['prop2']);
        
        $this->assertCount(2, $webSocketRequest->getTags());
        $this->assertArrayHasKey('tag1', $webSocketRequest->getTags());
        $this->assertArrayHasKey('tag2', $webSocketRequest->getTags());
        $this->assertSame('tag1Value', $webSocketRequest->getTags()['tag1']);
        $this->assertSame('tag2Value', $webSocketRequest->getTags()['tag2']);
        
        $this->assertCount(2, $webSocketRequest->getMetadata());
        $this->assertArrayHasKey('met1', $webSocketRequest->getMetadata());
        $this->assertArrayHasKey('met2', $webSocketRequest->getMetadata());
        $this->assertSame('met1Value', $webSocketRequest->getMetadata()['met1']);
        $this->assertSame('met2Value', $webSocketRequest->getMetadata()['met2']);
    }
    
    /**
     * Test method for `createFromJSON($jsonString)`.
     */
    public function testCreateFromJSON()
    {
        $messageParser = new MessageParser();
        
        // Test with an invalid JSON string
        try {
            WebSocketRequest::createFromJSON(654, $messageParser);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('The decoded JSON string is not an array !', $iaex->getMessage());
        }
        
        // Test with a valid JSON string
        $webSocketRequest = WebSocketRequest::createFromJSON(
            '{
			    "message" : {
				    "type" : "MY_TYPE",
					"metadata" : {
					    "prop1" : "prop1Value",
						"prop2" : "prop2Value"
					}
				},
				"tags" : {
				    "tag1" : "tag1Value",
					"tag2" : "tag2Value"
				},
                "metadata" : {
                	"met1" : "met1Value",
                	"met2" : "met2Value"
                }
	        }',
            $messageParser
        );
        
        $this->assertSame('MY_TYPE', $webSocketRequest->getMessage()->getType());
        $this->assertCount(2, $webSocketRequest->getMessage()->getMetadata());
        $this->assertArrayHasKey('prop1', $webSocketRequest->getMessage()->getMetadata());
        $this->assertArrayHasKey('prop2', $webSocketRequest->getMessage()->getMetadata());
        $this->assertSame('prop1Value', $webSocketRequest->getMessage()->getMetadata()['prop1']);
        $this->assertSame('prop2Value', $webSocketRequest->getMessage()->getMetadata()['prop2']);
        
        $this->assertCount(2, $webSocketRequest->getTags());
        $this->assertArrayHasKey('tag1', $webSocketRequest->getTags());
        $this->assertArrayHasKey('tag2', $webSocketRequest->getTags());
        $this->assertSame('tag1Value', $webSocketRequest->getTags()['tag1']);
        $this->assertSame('tag2Value', $webSocketRequest->getTags()['tag2']);
        
        $this->assertCount(2, $webSocketRequest->getMetadata());
        $this->assertArrayHasKey('met1', $webSocketRequest->getMetadata());
        $this->assertArrayHasKey('met2', $webSocketRequest->getMetadata());
        $this->assertSame('met1Value', $webSocketRequest->getMetadata()['met1']);
        $this->assertSame('met2Value', $webSocketRequest->getMetadata()['met2']);
    }
}
