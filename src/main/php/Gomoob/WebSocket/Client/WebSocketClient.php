<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\IWebSocketRequest;

use WebSocket\Client;

/**
 * Class which defines a Web Socket client to communicate with the Gomoob Web Socket server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketClient extends AbstractWebSocketClient
{
    /**
     * The Textalk WebSocket client used to send messages.
     *
     * @SuppressWarnings(PHPMD)
     */
    protected $client;

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
}
