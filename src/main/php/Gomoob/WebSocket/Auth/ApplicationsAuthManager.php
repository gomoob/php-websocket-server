<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Auth;

use Gomoob\WebSocket\IAuthManager;
use Gomoob\WebSocket\IWebSocketRequest;

use Ratchet\ConnectionInterface;

use Symfony\Component\Yaml\Yaml;

/**
 * Authentication manager used to restrict accesses based on application parameters.
 *
 * This authentication manager is configured through a YAML file which allows to declare several applications. Each
 * application declared has the following parameters :
 *  * `key` a string which identifies an application ;
 *  * `secret` a secret key used to authorize connection openings and message sendings ;
 *  * `authorizeOpen` by default connection openings are authorized to allow Browser clients to receive messages, if
 *    this parameter is `false` then connections should also be opened with specific `key` and `secret` URL parameters.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 */
class ApplicationsAuthManager implements IAuthManager
{
    /**
     * Boolean used to indicate of connections opening is authorized by default.
     *
     * @var boolean
     */
    protected $authorizeOpen = true;
    
    /**
     * The parsed configuration.
     *
     * @var array
     */
    protected $configuration;
    
    /**
     * The parsed configuration file.
     *
     * @var string
     */
    protected $configurationFile;
    
    /**
     * An associative array which maps application `key` to application configuration.
     *
     * @var array
     */
    protected $keyMap = [];
    
    /**
     * Creates a new instance of the applications authorization manager.
     *
     * @param array $options Options used to configure the component, the following options are available :
     *  * `configurationFile` an absolute path to a YAML configuration file which declares the applications ;
     *  * `authorizeOpen` a boolean used to indicate if connection opening is authorized by default (default is `true`)
     *   ;
     *
     * @throws \InvalidArgumentException if the provided configuration file cannot be read or has an invalid format.
     */
    public static function factory(array $options = [])
    {
        return new ApplicationsAuthManager($options);
    }

    /**
     * Creates a new instance of the applications authorization manager.
     *
     * @param array $options Options used to configure the component, the following options are available :
     *  * `configurationFile` an absolute path to a YAML configuration file which declares the applications ;
     *  * `authorizeOpen` a boolean used to indicate if connection opening is authorized by default (default is `true`)
     *   ;
     *
     * @throws \InvalidArgumentException if the provided configuration file cannot be read or has an invalid format.
     */
    public function __construct(array $options = [])
    {
        // If a configuration file property is configured
        if (array_key_exists('configurationFile', $options)) {
            $this->readConfigurationFile($options['configurationFile']);
        }
        
        // If the 'authorizeOpen' option is configured
        if (array_key_exists('authorizeOpen', $options)) {
            $this->authorizeOpen = $options['authorizeOpen'];
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function authorizeOpen(ConnectionInterface $connection)
    {
        $authorized = $this->authorizeOpen;
        
        // If the default connection opening authorization mode is false then try to get specific 'key' and 'secret'
        // URL parameters which match a declared application
        if (!$authorized) {
            // Gets the Guzzle query
            $query = $connection->WebSocket->request->getQuery();
            
            // If specific 'key' and 'secret' URL parameters are provided
            $key = $query->get('key');
            $secret = $query->get('secret');
            
            if ($key !== null && array_key_exists($key, $this->keyMap)) {
                $application = $this->keyMap[$key];
                
                // First try to authorize with 'authorizeOpen' option
                $authorized = $application['authorizeOpen'];
                
                // Then try to authorize with 'secret' option
                if (!$authorized && $secret !== null) {
                    $authorized = $secret === $application['secret'];
                }
            }
        }
        
        return $authorized;
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeSend(ConnectionInterface $connection, IWebSocketRequest $webSocketRequest)
    {
        $authorize = false;
        
        // Gets the WebSocket request metadata
        $metadata = $webSocketRequest->getMetadata();
        
        // If 'key' and 'secret' metadata properties are provided
        if ($metadata &&
            array_key_exists('key', $metadata) &&
            array_key_exists('secret', $metadata) &&
            array_key_exists($metadata['key'], $this->keyMap)) {
            $key = $metadata['key'];
            $secret = $metadata['secret'];
            $application = $this->keyMap[$key];
            $authorize = $secret === $application['secret'];
        }

        return $authorize;
    }

    /**
     * Utility method used to read a configuration file and load its data.
     *
     * @param string $configurationFile the path to the configuration file to read.
     *
     * @throws \InvalidArgumentException if the provided configuration file cannot be read or has an invalid format.
     */
    protected function readConfigurationFile($configurationFile)
    {
        // The configuration file does not exist
        if (!file_exists($configurationFile)) {
            throw new \InvalidArgumentException(
                'The configuration file \'' . $configurationFile . '\' does not exist !'
            );
        }
            
        // The configuration file must be a file and not a folder
        if (!is_file($configurationFile)) {
            throw new \InvalidArgumentException(
                'The configuration file \'' . $configurationFile . '\' is not a valid file !'
            );
        }
            
        $configurationFileContents = file_get_contents($configurationFile);
        
        // Failed to open the configuration file
        if ($configurationFileContents === false) {
            throw new \InvalidArgumentException(
                'Failed to open configuration file \'' . $configurationFile . '\' !'
            );
        }
        
        // Backup a reference to the configuration file
        $this->configurationFile = $configurationFile;

        // Parse the YAML file
        $this->parseYamlString($configurationFileContents);
    }
    
    /**
     * Parse a YAML string file and initialize the property of the component.
     *
     * @param string $yamlString the YAML string file to parse.
     *
     * @throws \InvalidArgumentException If the provided YAML string file is not valid.
     */
    protected function parseYamlString($yamlString)
    {
        // Parse the YAML file
        $configuration = Yaml::parse($yamlString);
        
        // The parsed configuration must be an array
        if (!is_array($configuration)) {
            throw new \InvalidArgumentException(
                'Invalid configuration provided in configuration file !'
            );
        }
        
        // The YAML string must have an 'applications' key
        if (!array_key_exists('applications', $configuration)) {
            throw new \InvalidArgumentException(
                'No \'applications\' key found in provided configuration file !'
            );
        }
        
        // Read each application configuration
        $i = 1;
        
        foreach ($configuration['applications'] as $application) {
            // The 'key' property must exist
            if (!array_key_exists('key', $application)) {
                throw new \InvalidArgumentException(
                    'No \'key\' property found in application \'' . $i . '\' declared in the configuration file !'
                );
            }
            
            // The 'secret' property must exist
            if (!array_key_exists('secret', $application)) {
                throw new \InvalidArgumentException(
                    'No \'secret\' property found in application \'' . $i . '\' declared in the configuration file !'
                );
            }
            
            // The 'authorizeOpen' propety must exist
            if (!array_key_exists('authorizeOpen', $application)) {
                throw new \InvalidArgumentException(
                    'No \'authorizeOpen\' property found in application \'' . $i .
                    '\' declared in the configuration file !'
                );
            }
            
            $this->keyMap[$application['key']] = $application;
        }
        
        // Backups the YAML configuration
        $this->configuration = $configuration;
    }
}
