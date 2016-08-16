<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which defines a message parser, a message parser allow to ensure that a message has a right format before
 * bing forwarded.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IMessageParser
{
    /**
     * Parse a message array and create a message object.
     *
     * @param array An array which represents a serialized message.
     *
     * @return object An object which represents an unserialized message.
     *
     * @throws \InvalidArgumentException If the provided array message cannot be parsed / is not valid.
     */
    public function parse(array $arrayMessage);
}
