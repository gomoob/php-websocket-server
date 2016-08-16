<?php

/**
 * gomoob/php-websocket-server
*
* @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
* @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
*/
namespace Gomoob\WebSocket\Message;

use Gomoob\WebSocket\IMessageParser;

/**
 * Class which defines a message parser for the `Gomoob\WebSocket\Message\Message` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class MessageParser implements IMessageParser
{
    /**
     * {@inheritDoc}
     */
    public function parse(array $arrayMessage)
    {
        
        // Ensure the array contains only valid key names
        foreach (array_keys($arrayMessage) as $key) {
            if (!is_string($key) || !in_array($key, ['type', 'creationDate', 'metadata'])) {
                throw new \InvalidArgumentException('Unexpected property \'' . $key . '\' !');
            }
        }
        
        // The 'type' property is always mandatory
        if (!array_key_exists('type', $arrayMessage)) {
            throw new \InvalidArgumentException('No \'type\' property found !');
        }
        
        $message = null;
        
        // If the 'metadata' property is provided
        if (array_key_exists('metadata', $arrayMessage) && $arrayMessage['metadata'] !== null) {
            // The 'metadata' property is not an array
            if (!is_array($arrayMessage['metadata'])) {
                throw new \InvalidArgumentException('The \'metadata\' property is not an array !');
            }
            
            $message = new Message($arrayMessage['type'], $arrayMessage['metadata']);
        } else {
            $message = new Message($arrayMessage['type']);
        }

        return $message;
    }
}
