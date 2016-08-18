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
     * Gets the default metadata to set on all requests sent with the client, those metadata will be merged with the
     * metadata specified on the requests.
     *
     * **WARNING** If a metadata property specifically defined on a request has the same name as a default metadata
     *             property defined on the WebSocket client then the metadata property defined with the request
     *             overwrites the default metadata property.
     *
     * @return array the default metadata to sent on all requests sent with the client.
     */
    public function getDefaultMetadata();
    
    /**
     * Gets the default tags to set on all requests sent with the client, those tags will be merged with the
     * tags specified on the requests.
     *
     * **WARNING** If a tag property specifically defined on a request has the same name as a default tag
     *             property defined on the WebSocket client then the tag property defined with the request
     *             overwrites the default tag property.
     *
     * @return array the default tags to sent on all requests sent with the client.
     */
    public function getDefaultTags();

    /**
     * Sends a Web Socket request to the Gomoob Web Socket server.
     *
     * @param \Gomoob\WebSocket\IWebSocketRequest $webSocketRequest the Web Socket request to send.
     *
     * @return \Gomoob\WebSocket\IWebSocketResponse the resulting Web Socket response.
     */
    public function send(IWebSocketRequest $webSocketRequest);
    
    /**
     * Sets default metadata to set on all requests sent with the client, those metadata will be merged with the
     * metadata specified on the requests.
     *
     * **WARNING** If a metadata property specifically defined on a request has the same name as a default metadata
     *             property defined on the WebSocket client then the metadata property defined with the request
     *             overwrites the default metadata property.
     *
     * @param array $defaultMetadata default metadata to sent on all requests sent with the client.
     *
     * @return \Gomoob\WebSocket\IWebSocketClient this instance.
     */
    public function setDefaultMetadata(array $defaultMetadata = []);
    
    /**
     * Sets the default tags to set on all requests sent with the client, those tags will be merged with the
     * tags specified on the requests.
     *
     * **WARNING** If a tag property specifically defined on a request has the same name as a default tag
     *             property defined on the WebSocket client then the tag property defined with the request
     *             overwrites the default tag property.
     *
     * @param array $defaultTags the default tags to sent on all requests sent with the client.
     *
     * @return \Gomoob\WebSocket\IWebSocketClient this instance.
     */
    public function setDefaultTags(array $defaultTags = []);
}
