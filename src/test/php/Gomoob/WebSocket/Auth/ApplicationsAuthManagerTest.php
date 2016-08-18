<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Client;

use Gomoob\WebSocket\Auth\ApplicationsAuthManager;
use Gomoob\WebSocket\Request\WebSocketRequest;

use Guzzle\Http\Message\Request;

use PHPUnit\Framework\TestCase;

use Ratchet\Server\IoConnection;

/**
 * Test case used to test the `\Gomoob\WebSocket\Auth\ApplicationsAuthManager` class.
 *
 * @author GOMOOB SARL (contact@gomoob.com)
 * @group WebSocketClientTest
 */
class ApplicationsAuthManagerTest extends TestCase
{
    /**
     * Test method for `authorizeOpen(ConnectionInterface $connection)`.
     */
    public function testAuthorizeOpen()
    {
        // Test without specifying `authorizeOpen` explicitly
        $this->assertTrue(ApplicationsAuthManager::factory()->authorizeOpen($this->createIoConnection()));
        
        // Test with explicit `authorizeOpen` set to true
        $this->assertTrue(
            ApplicationsAuthManager::factory(['authorizeOpen' => true])->authorizeOpen($this->createIoConnection())
        );
        
        // Test with explicit `authorizeOpen` set to false and no configuration file specified
        $this->assertFalse(
            ApplicationsAuthManager::factory(['authorizeOpen' => false])->authorizeOpen($this->createIoConnection())
        );

        // Test with explicit `authorizeOpen` set to false an a configuration file but no 'key' and 'secret' URL
        // parameters provided an connection
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen($this->createIoConnection())
        );
        
        // Test with explicit `authorizeOpen` set to false an a configuration file and 'key' / 'secret' URL
        // parameters but invalid 'key'
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen($this->createIoConnection('ws://localhost:8080?key=invalid&secret=XXXXXXXX'))
        );
        
        // Test with explicit `authorizeOpen` set to false an a configuration file and 'key' / 'secret' URL
        // parameters,  valid 'key', no 'secret' and 'authorizeOpen' on application set to true
        $this->assertTrue(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen($this->createIoConnection('ws://localhost:8080?key=application1'))
        );
        
        // Test with explicit `authorizeOpen` set to false an a configuration file and 'key' / 'secret' URL
        // parameters,  valid 'key', no 'secret' and 'authorizeOpen' on application set to true
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen($this->createIoConnection('ws://localhost:8080?key=application2'))
        );
        
        // Test with explicit `authorizeOpen` set to false and a configuration file and 'key' / 'secret' URL
        // parameters,  valid 'key', invalid 'secret' and 'authorizeOpen' on application set to false
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen($this->createIoConnection('ws://localhost:8080?key=application2&secret=XXXXXXXX'))
        );
        
        // Test with explicit `authorizeOpen` set to false and a configuration file and 'key' / 'secret' URL
        // parameters,  valid 'key', valid 'secret' and 'authorizeOpen' on application set to false
        $this->assertTrue(
            ApplicationsAuthManager::factory(
                [
                    'authorizeOpen' => false,
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeOpen(
                $this->createIoConnection(
                    'ws://localhost:8080?key=application2&secret=' .
                    'Kmrw5apmzmQMseAttckp6e7APeCDVtL58QzSPaKqqdHUF469hfhWyue3ns363kn5'
                )
            )
        );
    }
    
    /**
     * Test method for `authorizeSend(ConnectionInterface $connection, IWebSocketRequest $webSocketRequest)`.
     */
    public function testAuthorizeSend()
    {
        // Test without metadata parameters on request
        $this->assertFalse(
            ApplicationsAuthManager::factory()->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')
            )
        );
        
        // Test with metadata parameters on request but no 'key' and 'secret'
        $this->assertFalse(
            ApplicationsAuthManager::factory()->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')->setMetadata(
                    [
                        'met1' => 'met1Value',
                        'met2' => 'met2Value'
                    ]
                )
            )
        );
        
        // Test with 'key' and 'secret' metadata properties but no configuration file and so no application
        $this->assertFalse(
            ApplicationsAuthManager::factory()->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')->setMetadata(
                    [
                        'key' => 'unknown',
                        'secret' => 'XXXXXXXX'
                    ]
                )
            )
        );
        
        // Test with 'key' and 'secret' metadata properties but unknown 'key'
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')->setMetadata(
                    [
                        'key' => 'unknown',
                        'secret' => 'XXXXXXXX'
                    ]
                )
            )
        );
        
        // Test with 'key' and 'secret' metadata properties but invalid 'secret'
        $this->assertFalse(
            ApplicationsAuthManager::factory(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')->setMetadata(
                    [
                        'key' => 'application1',
                        'secret' => 'XXXXXXXX'
                    ]
                )
            )
        );
        
        // Test with 'key' and 'secret' metadata properties and valid 'secret'
        $this->assertTrue(
            ApplicationsAuthManager::factory(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
                ]
            )->authorizeSend(
                $this->createIoConnection(),
                WebSocketRequest::factory('Hello !')->setMetadata(
                    [
                        'key' => 'application1',
                        'secret' => '7UxuWw3ZcFBW85U2rdtjKZeStMHKVAzf8jpqkb5eAPBkd37F2sz4x3WS3GnMk7gq'
                    ]
                )
            )
        );
    }

    /**
     * Test method for `__construct(array $options = [])`.
     */
    public function testConstruct()
    {
        $ioConnection = $this->createIoConnection();
        $webSocketRequest = WebSocketRequest::factory('Hello World !');

        // Test with no options configured
        $authManager = new ApplicationsAuthManager();

        // Test with a YAML configuration file which does not exists
        try {
            new ApplicationsAuthManager(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/unknown.yml'
                ]
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame(
                'The configuration file \'' . TEST_RESOURCES_DIRECTORY . '/unknown.yml\' does not exist !',
                $iaex->getMessage()
            );
        }
        
        // Test with a YAML configuration file which is a folder
        try {
            new ApplicationsAuthManager(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY
                ]
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame(
                'The configuration file \'' . TEST_RESOURCES_DIRECTORY . '\' is not a valid file !',
                $iaex->getMessage()
            );
        }
        
        // Test with a configuration file which is not a well formed YAML file
        try {
            new ApplicationsAuthManager(
                [
                    'configurationFile' => TEST_RESOURCES_DIRECTORY . '/bad-auth.txt'
                ]
            );
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame(
                'Invalid configuration provided in configuration file !',
                $iaex->getMessage()
            );
        }
        
        // Test with a valid configuration file
        $authManager = new ApplicationsAuthManager(
            [
                'configurationFile' => TEST_RESOURCES_DIRECTORY . '/auth.yml'
            ]
        );
    }
    
    /**
     * Create a testing Ratchet `IoConnection` object.
     *
     * @param string $url the URL used to create the connection.
     *
     * @return \Ratchet\Server\IoConnection the resulting testing connection.
     */
    private function createIoConnection($url = 'ws://localhost:8080')
    {
        // Create a mocked React connection
        $reactConnection = $this->createMock(\React\Socket\Connection::class);
        $ioConnection = new IoConnection($reactConnection);
        
        // Creates a mock WebSocket with a testing Guzzle Request object
        $ioConnection->WebSocket = new \stdClass();
        $ioConnection->WebSocket->request = new Request('GET', $url);

        return $ioConnection;
    }
}
