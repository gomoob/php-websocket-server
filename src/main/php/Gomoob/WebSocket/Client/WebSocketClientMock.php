<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\IWebSocketRequest;
use Gomoob\WebSocket\Util\TagsTree;

use WebSocket\Client;

/**
 * Class which defines a Web Socket client mock to allow unit test writings without a server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketClientMock extends AbstractWebSocketClient
{
    /**
     * A structure which maps `{tagName,tagValue}` to associated Ratchet connections.
     *
     * @var \Gomoob\WebSocket\Util\TagsTree
     */
    protected $tagsTree = null;
    
    /**
     * Creates a new instance of the Gomoob Web Socket client mock.
     */
    public function __construct()
    {
        $this->tagsTree = new TagsTree();
    }
    
    /**
     * Checks if the WebSocket client mock contains a specific WebSocket request.
     *
     * @param \Gomoob\WebSocket\IWebSocketRequest $webSocketRequest the WebSocket request to test.
     *
     * @return boolean `true` if the provided WebSocket request has been sent by the client, `false` otherwise.
     */
    public function contains(IWebSocketRequest $webSocketRequest)
    {
        return $this->tagsTree->contains($webSocketRequest);
    }
    
    /**
     * Counts the total number of WebSocket requests sent by the client.
     *
     * @return int the total number of WebSocket requests sent by the client.
     */
    public function count()
    {
        return $this->tagsTree->count();
    }
    
    /**
     * Finds WebSocket requests sent with specific tags.
     *
     * @param array $tags the tags used to do the search.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest[] the found WebSocket requests.
     */
    public function findByTags(array $tags = [])
    {
        return $this->tagsTree->findByTags($tags);
    }
    
    /**
     * Resets the WebSocket client mock.
     */
    public function reset()
    {
        $this->tagsTree->reset();
    }

    /**
     * {@inheritDoc}
     */
    public function send(IWebSocketRequest $webSocketRequest)
    {
        $this->tagsTree->add($webSocketRequest, $webSocketRequest->getTags());
    }
}
