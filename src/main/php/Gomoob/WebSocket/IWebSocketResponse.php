<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which represents a Gomoob Web Socket response.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IWebSocketResponse
{
    /**
     * Function used to indicate if the response is a successful response.
     *
     * @return boolean `true` if the response is a successful response.
     */
    public function isOk();
}
