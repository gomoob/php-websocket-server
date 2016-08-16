<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which represents a Gomoob Web Socket request.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IWebSocketRequest extends \JsonSerializable
{
    /**
     * Gets the message to be sent on the Web Socket connections identified by the Web Socket server.
     *
     * @return string | \JsonSerializable the message to be sent on the Web Socket connections identified by the Web
     *         Socket server.
     */
    public function getMessage();
    
    /**
     * Gets the tags used to indicate to the Gomoob Web Socket server on which Web Socket connections to send the
     * message.
     *
     * @return array the tags used to indicate to the Gomoob Web Socket server on which Web Socket connections to send
     *         the message.
     */
    public function getTags();

    /**
     * Sets the message to be sent on the Web Socket connections identified by the Web Socket server.
     *
     * @param string | \JsonSerializable $message the message to be sent on the Web Socket connections identified by the
     *        Web Socket server.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest this instance.
     */
    public function setMessage($message);
    
    /**
     * Gets the additional metadata to transport with the WebSocket request.
     *
     * @return array the additional metadata to transport with the WebSocket request.
     */
    public function getMetadata();

    /**
     * Sets the tags used to indicate to the Gomoob Web Socket server on which Web Socket connections to send the
     * message.
     *
     * @param array $tags the tags used to indicate to the Gomoob Web Socket server on which Web Socket connections to
     *        send the message.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest this instance.
     */
    public function setTags(array $tags = []);

    /**
     * Sets the additional metadata to transport with the WebSocket request.
     *
     * @param array $metadata the additional metadata to transport with the WebSocket request.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest this instance.
     */
    public function setMetadata(array $metadata = []);
}
