<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Message;

use Gomoob\WebSocket\IMessage;

/**
 * Class which defines a generic "Message", the purpose of a message is to easily transport informations in the body
 * of messages systems while keeping a very simple model.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class Message implements IMessage
{
    /**
     * The metadata transported with the message.
     *
     * @var array
     */
    protected $metadata = [];

    /**
     * The type of the message.
     *
     * @var string
     */
    protected $type = null;

    /**
     * Creates a new message instance.
     *
     * @param string $type (Optional) the type of the message, this is always mandatory.
     * @param array $metadata (Optional) additional metadata to transport with the message, in most cases this is a
     *        simple key/value array.
     */
    public function __construct($type = null, array $metadata = [])
    {
        $this->type = $type;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
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
    public function jsonSerialize()
    {
        $data = [];

        // If the type is set
        if ($this->type) {
            $data['type'] = $this->type;
        }

        $data['metadata'] = $this->metadata;

        return $data;
    }

    /**
     * Creates a new message instance.
     *
     * @param string $type (Optional) the type of the message, this is always mandatory.
     * @param array $metadata (Optiona) additional metadata to transport with the message, in most cases this is a
     *        simple key/value array.
     */
    public static function create($type = null, array $metadata = [])
    {
        return new Message($type, $metadata);
    }
}
