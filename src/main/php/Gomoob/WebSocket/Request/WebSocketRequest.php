<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Request;

use Gomoob\WebSocket\IWebSocketRequest;
use Gomoob\WebSocket\IMessageParser;

/**
 * Interface which represents a Gomoob Web Socket request.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketRequest implements IWebSocketRequest
{
    /**
     * The message to be sent with the Web Socket connections associated to the Web Socket request tags.
     *
     * @var \Gomoob\Model\IMessage
     */
    protected $message;
    
    /**
     * The metadata to transport with the WebSocket request.
     *
     * **NOTE** Metadata are different than tags and do not fullfill the same goals :
     *  * They are not used to identify / pick WebSocket connections ;
     *  * They should not be used provide informations on client side, the metadata are never forwarded to clients and
     *    are only used by the WebSocket server ;
     *  * They should be used to help specific Gomoob WebSocket server components to work, for exemple an authorization
     *    manager could use specific `key` and `secret` metadata properties to manage its authorizations ;
     *  * The type of their properties are not restricted to `int` or `string` as tags, metadata properties can be of
     *    any primitive type or arrays of primitive types (arrays of any depth are authorized).
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * The tags associated to the Web Socket request, on server side the tags are used to identify Web Socket
     * connections to which one to send the associated message.
     *
     * @var array
     */
    protected $tags = [];
    
    /**
     * Creates a new instance of the WebSocket request.
     *
     * **NOTE** This function is an alias of the `factory($message)` function.
     *
     * @param string | \JsonSerializable $message the message to be sent with the Web Socket connections associated to
     *        the Web Socket request tags.
     */
    public static function create($message)
    {
        return static::factory($message);
    }
    
    /**
     * Creates a new instance of the WebSocket request from an array representation.
     *
     * @param array $jsonArray the JSON array used to create the `WebSocketRequest`.
     * @param \Gomoob\WebSocket\IMessageParser $messageParser (Optional) a parser used to transform the serialized
     *        version of the `message` property into a PHP object. The resulting PHP object is in most cases a Data
     *        Model object specific to an application.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest the resulting WebSocket request.
     *
     * @throws \InvalidArgumentException If the provided array is not valid.
     */
    public static function createFromArray(array $jsonArray, IMessageParser $messageParser = null)
    {
        // Ensure the array contains only valid key names
        foreach (array_keys($jsonArray) as $key) {
            if (!is_string($key) || !in_array($key, ['message', 'tags', 'metadata'])) {
                throw new \InvalidArgumentException('Unexpected property \'' . $key . '\' !');
            }
        }

        // The 'message' property is mandatory
        if (!array_key_exists('message', $jsonArray)) {
            throw new \InvalidArgumentException('The \'message\' property is mandatory !');
        }

        // If the 'message' property is not a string then a message parser must be configured
        if ($jsonArray !== null && !is_string($jsonArray['message']) && !$messageParser) {
            throw new \InvalidArgumentException(
                'The \'message\' property is not a string, you must configure a message parser to parse messages !'
            );
        }

        // Parse the wrapped message
        $message = null;
        
        // Simple message
        if ($jsonArray === null || is_string($jsonArray['message'])) {
            $message = $jsonArray['message'];
        } // Object message
        else {
            try {
                $message = $messageParser->parse($jsonArray['message']);
            } catch (\InvalidArgumentException $iaex) {
                throw new \InvalidArgumentException(
                    'Invalid \'message\' property !',
                    -1,
                    $iaex
                );
            }
        }

        // Creates the WebSocket request
        $webSocketRequest = new WebSocketRequest($message);

        // Parse the metadata
        if (array_key_exists('metadata', $jsonArray)) {
            $webSocketRequest->setMetadata($jsonArray['metadata']);
        }

        // Parse the tags
        if (array_key_exists('tags', $jsonArray)) {
            $webSocketRequest->setTags($jsonArray['tags']);
        }

        return $webSocketRequest;
    }
    
    /**
     * Creates a new instance of the WebSocket request from a JSON string.
     *
     * @param string $jsonString the JSON string to parse.
     * @param \Gomoob\WebSocket\IMessageParser $messageParser (Optional) a parser used to transform the serialized
     *        version of the `message` property into a PHP object. The resulting PHP object is in most cases a Data
     *        Model object specific to an application.
     *
     * @return \Gomoob\WebSocket\IWebSocketRequest the resulting WebSocket request.
     */
    public static function createFromJSON($jsonString, IMessageParser $messageParser = null)
    {
        // Try to decode the JSON string
        $array = json_decode($jsonString, true);
        
        // Decoding failed
        if ($array === null) {
            throw new \InvalidArgumentException('Failed to decode the provided JSON string !');
        }
        
        // The decoded value must always be an array
        if (!is_array($array)) {
            throw new \InvalidArgumentException('The decoded JSON string is not an array !');
        }
        
        // Now decode the array
        return static::createFromArray($array, $messageParser);
    }
    
    /**
     * Creates a new instance of the WebSocket request.
     *
     * @param string | \JsonSerializable $message the message to be sent with the Web Socket connections associated to
     *        the Web Socket request tags.
     */
    public static function factory($message)
    {
        return new WebSocketRequest($message);
    }
    
    /**
     * Creates a new instance of the WebSocket request.
     *
     * @param string | \JsonSerializable $message the message to be sent with the Web Socket connections associated to
     *        the Web Socket request tags.
     * @param array $tags (Optional) the tags associated to the Web Socket request, on server side the tags are used to
     *        identify Web Socket connections to which one to send the associated message.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setTags(array $tags = [])
    {
        // Validates the tags
        $this->validateTags($tags);
        
        // Sets the tags
        $this->tags = $tags;
        
        // Returns this instance
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setMetadata(array $metadata = [])
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        // Serialize message, suppose string by default
        $data = ['message' => $this->message];
        if ($this->message instanceof \JsonSerializable) {
            $data['message'] = $this->message->jsonSerialize();
        }

        // Serialize metadata, if empty serialize as an empty object
        $data['metadata'] = $this->metadata;
        if (!$data['metadata']) {
            $data['metadata'] = new \stdClass();
        }

        // Serialize tags, if empty serialize as an empty object
        $data['tags'] = $this->tags;
        if (!$data['tags']) {
            $data['tags'] = new \stdClass();
        }

        return $data;
    }
    
    /**
     * Utility method used to ensure that a `tags` array contains only `string => int` or `string => string`.
     *
     * @param array $tags the tags array to validate.
     */
    protected function validateTags(array $tags)
    {
        foreach ($tags as $tagName => $tagValue) {
            // Tag names must only be strings
            if (!is_string($tagName)) {
                throw new \InvalidArgumentException('The tag name \'' . $tagName . '\' is not a string !');
            }
    
            // Tag values must only be integers or string
            if (!(is_int($tagValue) || is_string($tagValue))) {
                throw new \InvalidArgumentException(
                    'The tag named \'' . $tagName . '\' has a value which is not an integer or a string !'
                );
            }
        }
    }
}
