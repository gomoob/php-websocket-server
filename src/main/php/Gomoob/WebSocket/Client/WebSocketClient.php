<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\IWebSocketClient;
use Gomoob\WebSocket\IWebSocketRequest;

use WebSocket\Client;

/**
 * Class which defines a Web Socket client to communicate with the Gomoob Web Socket server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketClient implements IWebSocketClient
{
    /**
     * The Textalk WebSocket client used to send messages.
     *
     * @SuppressWarnings(PHPMD)
     */
    protected $client;

    /**
     * Default metadata to set on all requests sent with the client, those metadata will be merged with the
     * metadata specified on the requests.
     *
     * **WARNING** If a metadata property specifically defined on a request has the same name as a default metadata
     *             property defined on the WebSocket client then the metadata property defined with the request
     *             overwrites the default metadata property.
     *
     * @var array
     */
    protected $defaultMetadata = [];
    
    /**
     * Default tags to set on all requests sent with the client, those tags will be merged with the tags specified on
     * the requests.
     *
     * **WARNING** If a tag property specifically defined on a request has the same name as a default tag
     *             property defined on the WebSocket client then the tag property defined with the request
     *             overwrites the default tag property.
     * @var array
     */
    protected $defaultTags = [];
    
    /**
     * Creates a new instance of the Gomoob Web Socket client.
     *
     * @param string $uri the ws/wss URI used to connect to the Gomoob WebSocket server.
     */
    public function __construct($uri)
    {
        $this->client = new Client($uri);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultMetadata()
    {
        return $this->defaultMetadata;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDefaultTags()
    {
        return $this->defaultTags;
    }

    /**
     * {@inheritdoc}
     */
    public function send(IWebSocketRequest $webSocketRequest)
    {
        // The Web Socket resquest must have a message
        if (!$webSocketRequest->getMessage()) {
            throw new \InvalidArgumentException('The provided Web Socket request must have a message !');
        }
        
        // Create a cloned WebSocket request to update it before sending it
        $clonedWebSocketRequest = clone $webSocketRequest;

        // Merge default metadata
        $clonedWebSocketRequest->setMetadata(
            array_merge(
                $this->defaultMetadata,
                $webSocketRequest->getMetadata()
            )
        );

        // Merge default tags
        $clonedWebSocketRequest->setTags(
            array_merge(
                $this->defaultTags,
                $webSocketRequest->getTags()
            )
        );

        // Sends the request
        $this->client->send(json_encode($clonedWebSocketRequest));
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultMetadata(array $defaultMetadata = [])
    {
        $this->defaultMetadata = $defaultMetadata;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDefaultTags(array $defaultTags = [])
    {
        $this->defaultTags = $defaultTags;
        
        return $this;
    }
}
