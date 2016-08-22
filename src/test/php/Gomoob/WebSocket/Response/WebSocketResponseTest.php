<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Response;

use Gomoob\WebSocket\Response\WebSocketResponse;

use PHPUnit\Framework\TestCase;

/**
 * Test case used to test the `\Gomoob\WebSocket\Response\WebSocketResponse` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 * @group WebSocketRequestTest
 */
class WebSocketResponseTest extends TestCase
{
    /**
     * Test method for `isOk()`.
     */
    public function testIsOk()
    {
        $webSocketResponse = new WebSocketResponse();
        
        // For now response is always OK
        $this->assertTrue($webSocketResponse->isOk());
    }
}
