<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server;

use Guzzle\Http\QueryString;

/**
 * Utility class used to parse a `tags` URL parameter set when a Browser Client opens a WebSocket connection.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 */
class QueryStringTagsParser
{
    /**
     * Utility method used to process a Guzzle query string object and extracts `tags` from it.
     *
     * @param \Guzzle\Http\QueryString $queryString the Guzzle query string to parse.
     *
     * @return array the parsed tags.
     */
    public static function parse(QueryString $queryString)
    {
        $tags = [];
        $queryStringArray = $queryString->toArray();
        
        // If a 'tags' URL parameter exists
        if (array_key_exists('tags', $queryStringArray)) {
            // Decode the 'tags' URL parameter
            $tagsJson = json_decode($queryStringArray['tags'], true);
                
            // The decoding failed
            if ($tagsJson === false) {
                throw new \InvalidArgumentException('The \'tags\' URL parameter is not a valid JSON string !');
            } // The 'tags' URL parameter must be a JSON array
            elseif (!is_array($tagsJson)) {
                throw new \InvalidArgumentException('The \'tags\' URL parameter is not a JSON array !');
            } // Otherwise the 'tags' URL parameter is a JSON array
            else {
                // Parse each tag
                foreach ($tagsJson as $tagName => $tagValue) {
                    // Each tag value must be an integer or a string, all other types are forbidden
                    if (!(is_int($tagValue) || is_string($tagValue))) {
                        throw new \InvalidArgumentException(
                            'The \'' . $tagName . '\' tag is not an integer or a string !'
                        );
                    }
                    
                    $tags[$tagName] = $tagValue;
                }
            }
        }
        
        return $tags;
    }
}
