<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which defines a Web Socket client to communicate with the Gomoob Web Socket server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IWebSocketClient
{
    /**
     * Sends a Web Socket request to the Gomoob Web Socket server.
     *
     * @param \Gomoob\WebSocket\IWebSocketRequest $webSocketRequest the Web Socket request to send.
     *
     * @return \Gomoob\WebSocket\IWebSocketResponse the resulting Web Socket response.
     */
    public function send(IWebSocketRequest $webSocketRequest);
}
