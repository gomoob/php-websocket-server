<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\Request\WebSocketRequest;

use PHPUnit\Framework\TestCase;

/**
 * Test case used to test the `\Gomoob\WebSocket\Client\WebSocketClientMock` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 * @group WebSocketClientMockTest
 */
class WebSocketClientMockTest extends TestCase
{
    /**
     * Test method for `contains(IWebSocketRequest $webSocketRequest)`.
     *
     * @group WebSocketClientMockTest.testContains
     */
    public function testContains()
    {
        $webSocketClientMock = new WebSocketClientMock();
    
        $webSocketRequest0 = WebSocketRequest::create('Message 0.');
        $webSocketRequest1 = WebSocketRequest::create('Message 1.');
        $webSocketRequest2 = WebSocketRequest::create('Message 2.');
        
        // At the begining the mock contains nothing
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest0));
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest1));
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest2));
        
        $webSocketClientMock->send($webSocketRequest0);
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest0));
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest1));
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest2));
        
        $webSocketClientMock->send($webSocketRequest1);
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest0));
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest1));
        $this->assertFalse($webSocketClientMock->contains($webSocketRequest2));
        
        $webSocketClientMock->send($webSocketRequest2);
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest0));
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest1));
        $this->assertTrue($webSocketClientMock->contains($webSocketRequest2));
    }
    
    /**
     * Test method for `count()`.
     *
     * @group WebSocketClientMockTest.testCount
     */
    public function testCount()
    {
        $webSocketClientMock = new WebSocketClientMock();
    
        $webSocketRequest0 = WebSocketRequest::create('Message 0.');
        $webSocketRequest1 = WebSocketRequest::create('Message 1.');
        $webSocketRequest2 = WebSocketRequest::create('Message 2.');
        
        $this->assertSame(0, $webSocketClientMock->count());
        
        $webSocketClientMock->send($webSocketRequest0);
        $this->assertSame(1, $webSocketClientMock->count());
        
        $webSocketClientMock->send($webSocketRequest1);
        $this->assertSame(2, $webSocketClientMock->count());
        
        $webSocketClientMock->send($webSocketRequest2);
        $this->assertSame(3, $webSocketClientMock->count());
    }
    
    /**
     * Test method for `findByTags(array $tags = [])`.
     *
     * @group WebSocketClientMockTest.testFindByTags
     */
    public function testFindByTags()
    {
        $webSocketClientMock = new WebSocketClientMock();
    
        $webSocketRequest0 = WebSocketRequest::create('Message 0.')->setTags(['tag0' => 'tag0Value']);
        $webSocketRequest1 = WebSocketRequest::create('Message 1.')->setTags(['tag1' => 'tag1Value']);
        $webSocketRequest2 = WebSocketRequest::create('Message 2.')
            ->setTags(['tag0' => 'tag0Value', 'tag1' => 'tag1Value']);

        $webSocketClientMock->send($webSocketRequest0);
        $webSocketClientMock->send($webSocketRequest1);
        $webSocketClientMock->send($webSocketRequest2);

        // Finding with no filter returns all objects
        $webSocketRequests = $webSocketClientMock->findByTags();
        $this->assertCount(3, $webSocketRequests);
        $this->assertContains($webSocketRequest0, $webSocketRequests);
        $this->assertContains($webSocketRequest1, $webSocketRequests);
        $this->assertContains($webSocketRequest2, $webSocketRequests);
        
        // Test with 'tag0' => 'tag0Value'
        $webSocketRequests = $webSocketClientMock->findByTags(['tag0' => 'tag0Value']);
        $this->assertCount(2, $webSocketRequests);
        $this->assertContains($webSocketRequest0, $webSocketRequests);
        $this->assertNotContains($webSocketRequest1, $webSocketRequests);
        $this->assertContains($webSocketRequest2, $webSocketRequests);
        
        // Test with 'tag1' => 'tag1Value'
        $webSocketRequests = $webSocketClientMock->findByTags(['tag1' => 'tag1Value']);
        $this->assertCount(2, $webSocketRequests);
        $this->assertNotContains($webSocketRequest0, $webSocketRequests);
        $this->assertContains($webSocketRequest1, $webSocketRequests);
        $this->assertContains($webSocketRequest2, $webSocketRequests);
    }
    
    /**
     * Test method for `reset()`.
     *
     * @group WebSocketClientMockTest.testReset
     */
    public function testReset()
    {
        $webSocketClientMock = new WebSocketClientMock();
    
        $webSocketRequest0 = WebSocketRequest::create('Message 0.');
        $webSocketRequest1 = WebSocketRequest::create('Message 1.');
        $webSocketRequest2 = WebSocketRequest::create('Message 2.');
    
        $webSocketClientMock->send($webSocketRequest0);
        $webSocketClientMock->send($webSocketRequest1);
        $webSocketClientMock->send($webSocketRequest2);
        $this->assertSame(3, $webSocketClientMock->count());
        
        $webSocketClientMock->reset();
        $this->assertSame(0, $webSocketClientMock->count());
    }
}
