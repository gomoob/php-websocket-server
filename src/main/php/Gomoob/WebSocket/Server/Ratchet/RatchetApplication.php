<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server\Ratchet;

use Gomoob\WebSocket\Request\WebSocketRequest;

use Gomoob\WebSocket\Server\QueryStringTagsParser;
use Gomoob\WebSocket\Server\TagsTree;

use Monolog\Logger;

use Psr\Log\LoggerInterface;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * The Ratchet application.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 */
class RatchetApplication implements MessageComponentInterface
{
    /**
     * The component used to authorize WebSocket connection opening and WebSocket message sendings.
     *
     * @var \Gomoob\WebSocket\IAuthManager
     */
    protected $authManager;
    
    /**
     * The PSR logger used in the application.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * The component used to parse messages.
     *
     * @var \Gomoob\WebSocket\IMessageParser
     */
    protected $messageParser;

    /**
     * A structure which maps `{tagName,tagValue}` to associated Ratchet connections.
     *
     * @var \Gomoob\WebSocket\TagsTree
     */
    protected $tagsTree = null;

    /**
     * Creates a new Ratchet application instance.
     *
     * @param array $options (Optional) options used to configure the Ratchet application, the following options are
     *        supported :
     *        * `logger` A PSR logger used to log error and debug messages ;
     *        * `messageParser` A component used to parse messages ;
     *        * `authManager` A component used to manage authorizations.
     */
    public function __construct(array $options = [])
    {
        // Initialize the authorization manager
        if (array_key_exists('authManager', $options)) {
            $this->authManager = $options['authManager'];
        }

        // Initialize the logger
        $this->logger = new Logger('WebSocketServer');

        if (array_key_exists('logger', $options)) {
            $this->logger = $options['logger'];
        }
        
        // Initialize the message parser
        if (array_key_exists('messageParser', $options)) {
            $this->messageParser = $options['messageParser'];
        }
        
        $this->tagsTree = new TagsTree();
    }

    /**
     * {@inheritdoc}
     */
    public function onOpen(ConnectionInterface $connection)
    {
        // First check if connection opening is authorized
        if ($this->authManager && !$this->authManager->authorizeOpen($connection)) {
            throw new \InvalidArgumentException('Connection opening is not authorized !');
        }
        
        // Parse the 'tags' URL parameter
        $tags = QueryStringTagsParser::parse($connection->WebSocket->request->getQuery());

        // Add the connections to the opened connections
        $this->tagsTree->add($connection, $tags);

        // In development mode we log a message
        $this->logger->debug(
            'New WebSocket connection opened with resource id \'{resourceId}\'.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage(ConnectionInterface $connection, $strMessage)
    {
        // In development mode we log a message
        $this->logger->debug(
            'Receiving message on WebSocket connection with resource id \'{resourceId}\'.',
            [
                'resourceId' => $connection->resourceId,
                'strMessage' => $strMessage
            ]
        );
        
        // Decodes the WebSocket request
        $webSocketRequest = null;
        
        try {
            $webSocketRequest = WebSocketRequest::createFromJSON($strMessage, $this->messageParser);
        } catch (\InvalidArgumentException $iaex) {
            $errorMessage = 'Fail to parse WebSocket request string !';
            $this->logger->debug($errorMessage, ['exception' => $iaex]);
            throw new \InvalidArgumentException($errorMessage, -1, $iaex);
        }
        
        // Checks if message sending is authorized
        if ($this->authManager && !$this->authManager->authorizeSend($connection, $webSocketRequest)) {
            $errorMessage =
                'Message sending is not authorized on WebSocket connection with resource id \'' .
                $connection->resourceId . '\' !';
            $this->logger->debug(
                $errorMessage,
                [
                    'resourceId' => $connection->resourceId,
                    'strMessage' => $strMessage
                ]
            );
            throw new \InvalidArgumentException($errorMessage);
        }
        
        // Gets the connections associated to the provided tags
        $connections = $this->tagsTree->findByTags($webSocketRequest->getTags());
        
        // Forward the message to the found connections
        foreach ($connections as $currentConnection) {
            $currentConnection->send(json_encode($webSocketRequest->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onClose(ConnectionInterface $connection)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->tagsTree->delete($connection);

        // In development mode we log a message
        $this->logger->debug(
            'WebSocket connection with resource id \'{resourceId}\' has closed.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ConnectionInterface $connection, \Exception $ex)
    {
        $this->logger->error(
            'Error encountered on WebSocket connection having resource id \'{resourceId}\'.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
        $this->logger->error(
            'Force to close WebSocket connection having resource id \'{resourceId}\'.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
        $connection->close();
        $this->logger->error(
            ' WebSocket connection having resource id \'{resourceId}\' closed.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
        $this->tagsTree->delete($connection);
        $this->logger->error(
            ' WebSocket connection having resource id \'{resourceId}\' deleted.',
            [
                'resourceId' => $connection->resourceId
            ]
        );
    }
}
