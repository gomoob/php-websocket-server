<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

use Ratchet\ConnectionInterface;

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
