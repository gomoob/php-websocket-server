<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Response;

use Gomoob\WebSocket\IWebSocketResponse;

/**
 * Class which represents a Web Socket response.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketResponse implements IWebSocketResponse
{
    /**
     * {@inheritDoc}
     */
    public function isOk()
    {
        return true;
    }
}
