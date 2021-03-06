<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server;

use Gomoob\WebSocket\IWebSocketServer;

use Gomoob\WebSocket\Server\Ratchet\RatchetApplication;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

/**
 * Class which defines a Gomoob Web Socket server.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 */
class WebSocketServer implements IWebSocketServer
{
    /**
     * The options used to create the WebSocket server.
     *
     * @var array
     */
    protected $options;
    
    /**
     * The Ratchet application to use.
     *
     * @var \Gomoob\WebSocket\Server\RatchetApplication
     */
    protected $ratchetApplication;
    
    /**
     * The Ratchet server in use.
     *
     * @var \Ratchet\Server\IoServer
     */
    protected $ratchetServer;
    
    /**
     * Creates a new Gomoob Web Socket server instance.
     *
     * **NOTE** This function is an alias of the `factory(array $options)` function.
     *
     * @param array $options (Optional) options used to configure the server, the following options are supported :
     *  * `port` The port to server sockets on string ;
     *  * `address` The address to receive sockets on (0.0.0.0 means receive connections from any) ;
     *  * `logger` A PSR logger used to log messages, the server only logs error and debug messages ;
     *  * `messageParser` A component used to parse messages ;
     *  * `authManager` A component used to manage authorizations.
     */
    public static function create(array $options = ['port' => 80, 'address' => '0.0.0.0'])
    {
        return static::factory($options);
    }

    /**
     * Creates a new Gomoob Web Socket server instance.
     *
     * @param array $options (Optional) options used to configure the server, the following options are supported :
     *  * `port` The port to server sockets on string ;
     *  * `address` The address to receive sockets on (0.0.0.0 means receive connections from any) ;
     *  * `logger` A PSR logger used to log messages, the server only logs error and debug messages ;
     *  * `messageParser` A component used to parse messages ;
     *  * `authManager` A component used to manage authorizations.
     */
    public static function factory(array $options = ['port' => 80, 'address' => '0.0.0.0'])
    {
        return new WebSocketServer($options);
    }

    /**
     * Creates a new Gomoob Web Socket server instance.
     *
     * @param array $options (Optional) options used to configure the server, the following options are supported :
     *  * `port` The port to server sockets on string ;
     *  * `address` The address to receive sockets on (0.0.0.0 means receive connections from any) ;
     *  * `logger` A PSR logger used to log messages, the server only logs error and debug messages ;
     *  * `messageParser` A component used to parse messages ;
     *  * `authManager` A component used to manage authorizations.
     */
    public function __construct(array $options = ['port' => 80, 'address' => '0.0.0.0'])
    {
        // Initialize the Ratchet application
        $this->ratchetApplication = new RatchetApplication($options);
        
        // Sets the options
        $this->options = $options;
        $this->options['port'] = array_key_exists('port', $options) ? $options['port'] : 80;
        $this->options['address'] = array_key_exists('address', $options) ? $options['address'] : '0.0.0.0';
    }
    
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Initialize the Ratchet server
        $this->ratchetServer = IoServer::factory(
            new HttpServer(
                new WsServer($this->ratchetApplication)
            ),
            $this->options['port'],
            $this->options['address']
        );

        // Runs the server
        $this->ratchetServer->run();
    }
}
