<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which defines a Gomoob Web Socket server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IWebSocketServer
{
    /**
     * Runs the server.
     */
    public function run();
}
