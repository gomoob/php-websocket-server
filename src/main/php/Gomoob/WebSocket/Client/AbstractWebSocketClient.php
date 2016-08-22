<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\IWebSocketClient;

/**
 * Abstract class common to all WebSocket clients.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
abstract class AbstractWebSocketClient implements IWebSocketClient
{
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
