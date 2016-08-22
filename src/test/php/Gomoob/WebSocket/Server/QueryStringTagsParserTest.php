<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server;

use Guzzle\Http\QueryString;

use PHPUnit\Framework\TestCase;

/**
 * Test case for the `QueryStringTagsParser` class.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 * @group QueryStringTagsParserTest
 */
class QueryStringTagsParserTest extends TestCase
{
    /**
     * Test method for `parser(QueryString $queryString)`.
     *
     * @group QueryStringTagsParserTest.testParse
     */
    public function testParse()
    {
        // Test with no 'tags' parameter
        $this->assertEmpty(QueryStringTagsParser::parse(new QueryString()));
        
        // Test with a 'tags' parameter which is not a JSON array
        $queryString = new QueryString();
        $queryString->add('tags', 'abcde');
        
        try {
            QueryStringTagsParser::parse($queryString);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('The \'tags\' URL parameter is not a JSON array !', $iaex->getMessage());
        }
        
        // Test with a 'tags' which does not only contain string or integer tags
        $queryString = new QueryString();
        $queryString->add('tags', '{"prop":[{"a":1}]}');

        try {
            QueryStringTagsParser::parse($queryString);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $iaex) {
            $this->assertSame('The \'prop\' tag is not an integer or a string !', $iaex->getMessage());
        }

        // Test with valid tags
        $queryString = new QueryString();
        $queryString->add('tags', '{"prop1": 12, "prop2": "prop2Value"}');
        $tags = QueryStringTagsParser::parse($queryString);
        $this->assertCount(2, $tags);
        $this->assertArrayHasKey('prop1', $tags);
        $this->assertSame(12, $tags['prop1']);
        $this->assertArrayHasKey('prop2', $tags);
        $this->assertSame('prop2Value', $tags['prop2']);
    }
}
