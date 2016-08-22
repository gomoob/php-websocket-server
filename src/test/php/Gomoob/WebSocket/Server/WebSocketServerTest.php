<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server;

use PHPUnit\Framework\TestCase;

use Ratchet\Server\IoServer;

/**
 * Test case for the `WebSocketServer` class.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 * @group WebSocketServerTest
 */
class WebSocketServerTest extends TestCase
{
    /**
     * Test method for `create(array $options = ['port' => 80, 'address' => '0.0.0.0'])`.
     *
     * @group WebSocketServerTest.testCreate
     */
    public function testCreate()
    {
        // Test with default options
        $webSockerServer = WebSocketServer::create();
        $this->assertNotNull($webSockerServer);
    }
    
    /**
     * Test method for `factory(array $options = ['port' => 80, 'address' => '0.0.0.0'])`.
     *
     * @group WebSocketServerTest.testFactory
     */
    public function testFactory()
    {
        // Test with default options
        $webSockerServer = WebSocketServer::factory();
        $this->assertNotNull($webSockerServer);
    }
}
