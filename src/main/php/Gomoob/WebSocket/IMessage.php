<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket;

/**
 * Interface which defines a generic "Message", the purpose of a message is to easily transport informations in the body
 * of messages systems while keeping a very simple model.
 *
 * An application should always define a clear specification to describe the messages it transports, this specification
 * should define explicit message type names for each message transported.
 *
 * In most cases a message will be transported using a JSON format (although its not mandatory), that's why this
 * interface extends the `\JsonSerializable` PHP interface.
 *
 * A message should be simple, it contains the following properties
 *
 *  * `type`         (Optional)  A string which expresses the message type, developer should choose explicit message
 *                               type names for their applications ;
 *  * `creationDate` (Optional)  An optional creation date expressed in ISO 8601 format ;
 *  * `metadata`     (Optional)  An optional array which allows to transport additional metadata.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
interface IMessage extends \JsonSerializable
{
    /**
     * Gets the type of the message, the type is always mandatory.
     *
     * @return string the type of the message.
     */
    public function getType();
    
    /**
     * Additional metadata transported with the message.
     *
     * @return array the additional metadata transported with the message.
     */
    public function getMetadata();
}
