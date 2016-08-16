<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Server;

use PHPUnit\Framework\TestCase;

/**
 * Test case for the `TagsTree` class.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 * @group TagsTreeTest
 */
class TagsTreeTest extends TestCase
{
    /**
     * Test method for `add($object, array $tags = [])`.
     *
     * @group TagsTreeTest.testAdd
     */
    public function testAdd()
    {
        $tagsTree = new TagsTree();
        
        // Test with an invalid tag key (i.e not a string)
        try {
            $tagsTree->add(new \DateTime(), [45 => 'aValue']);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $aex) {
            $this->assertSame(
                'The tag name \'45\' is not a string !',
                $aex->getMessage()
            );
        }
        
        // Test with an invalid tag value (i.e not an integer or a string)
        try {
            $tagsTree->add(new \DateTime(), ['tag' => new \DateTime()]);
            $this->fail('Must have thrown an InvalidArgumentException !');
        } catch (\InvalidArgumentException $aex) {
            $this->assertSame(
                'The tag named \'tag\' has a value which is not an integer or a string !',
                $aex->getMessage()
            );
        }
        
        // At the begining the structure is empty
        $this->assertSame(0, $tagsTree->count());
        
        // Add an object without tags
        $object0 = new \DateTime();
        $tagsTree->add($object0);
        
        $this->assertSame(1, $tagsTree->count());
        $this->assertCount(1, $tagsTree->findByTags());
        $this->assertContains($object0, $tagsTree->findByTags());
        
        // Add an object with tags
        $object1 = new \DateTime();
        $tagsTree->add($object1, ['tag1' => 'tag1Value']);
        
        $this->assertSame(2, $tagsTree->count());
        $this->assertCount(2, $tagsTree->findByTags());
        $this->assertContains($object0, $tagsTree->findByTags());
        $this->assertContains($object1, $tagsTree->findByTags());
    }
    
    /**
     * Test method for `contains($object)`.
     *
     * @group TagsTreeTest.testContains
     */
    public function testContains()
    {
        $object0 = new \DateTime();
        $object1 = new \DateTime();
        $object2 = new \DateTime();
        
        $tagsTree = new TagsTree();
        
        // Add the begining the tags tree is empty
        $this->assertFalse($tagsTree->contains($object0));
        $this->assertFalse($tagsTree->contains($object1));
        $this->assertFalse($tagsTree->contains($object2));
        
        // Add first object
        $tagsTree->add($object0);
        $this->assertTrue($tagsTree->contains($object0));
        $this->assertFalse($tagsTree->contains($object1));
        $this->assertFalse($tagsTree->contains($object2));
        
        // Add second object
        $tagsTree->add($object1, ['a' => 'aValue']);
        $this->assertTrue($tagsTree->contains($object0));
        $this->assertTrue($tagsTree->contains($object1));
        $this->assertFalse($tagsTree->contains($object2));
        
        // Add third object
        $tagsTree->add($object2, ['b' => 'bValue']);
        $this->assertTrue($tagsTree->contains($object0));
        $this->assertTrue($tagsTree->contains($object1));
        $this->assertTrue($tagsTree->contains($object2));
    }
    
    /**
     * Test method for `delete($object)`.
     *
     * @group TagsTreeTest.testDelete
     */
    public function testDelete()
    {
        $object0 = new \DateTime();
        $object1 = new \DateTime();
        $object2 = new \DateTime();
        
        $tagsTree = new TagsTree();
        $tagsTree->add($object0);
        $tagsTree->add($object1, ['a' => 'aValue']);
        $tagsTree->add($object2, ['b' => 'bValue']);
        
        // At the begining the tags tree contains 3 objects
        $this->assertTrue($tagsTree->contains($object0));
        $this->assertTrue($tagsTree->contains($object1));
        $this->assertTrue($tagsTree->contains($object2));
        $this->assertCount(1, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertContains($object1, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertCount(1, $tagsTree->findByTags(['b' => 'bValue']));
        $this->assertContains($object2, $tagsTree->findByTags(['b' => 'bValue']));
        
        
        // Delete first object
        $tagsTree->delete($object0);
        $this->assertFalse($tagsTree->contains($object0));
        $this->assertTrue($tagsTree->contains($object1));
        $this->assertTrue($tagsTree->contains($object2));
        $this->assertCount(1, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertContains($object1, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertCount(1, $tagsTree->findByTags(['b' => 'bValue']));
        $this->assertContains($object2, $tagsTree->findByTags(['b' => 'bValue']));
        
        // Delete second object
        $tagsTree->delete($object1);
        $this->assertFalse($tagsTree->contains($object0));
        $this->assertFalse($tagsTree->contains($object1));
        $this->assertTrue($tagsTree->contains($object2));
        $this->assertCount(0, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertCount(1, $tagsTree->findByTags(['b' => 'bValue']));
        $this->assertContains($object2, $tagsTree->findByTags(['b' => 'bValue']));
        
        // Delete third object
        $tagsTree->delete($object2);
        $this->assertFalse($tagsTree->contains($object0));
        $this->assertFalse($tagsTree->contains($object1));
        $this->assertFalse($tagsTree->contains($object2));
        $this->assertCount(0, $tagsTree->findByTags(['a' => 'aValue']));
        $this->assertCount(0, $tagsTree->findByTags(['b' => 'bValue']));
    }
    
    /**
     * Test method for `findByTags(array $tags = [])`.
     *
     * @group TagsTreeTest.testFindByTags
     */
    public function testFindByTags()
    {
        $object0 = new \DateTime();
        $object1 = new \DateTime();
        $object2 = new \DateTime();
        $object3 = new \DateTime();
        $object4 = new \DateTime();
        $object5 = new \DateTime();
    
        $tagsTree = new TagsTree();
        $tagsTree->add($object0);
        $tagsTree->add($object1, ['a' => 'aValue']);
        $tagsTree->add($object2, ['b' => 'bValue']);
        $tagsTree->add($object3, ['a' => 'aValue', 'b' => 'bValue']);
        $tagsTree->add($object4, ['a' => 'aValue', 'b' => 'bValue']);
        $tagsTree->add($object5, ['a' => 'aValue', 'b' => 'otherBValue']);

        // Test without parameters
        $objects = $tagsTree->findByTags();
        $this->assertCount(6, $objects);
        $this->assertContains($object0, $objects);
        $this->assertContains($object1, $objects);
        $this->assertContains($object2, $objects);
        $this->assertContains($object3, $objects);
        $this->assertContains($object4, $objects);
        $this->assertContains($object5, $objects);
        
        // Test with only 'a'
        $objects = $tagsTree->findByTags(['a' => 'aValue']);
        $this->assertCount(4, $objects);
        $this->assertNotContains($object0, $objects);
        $this->assertContains($object1, $objects);
        $this->assertNotContains($object2, $objects);
        $this->assertContains($object3, $objects);
        $this->assertContains($object4, $objects);
        $this->assertContains($object5, $objects);
        
        // Test with 'a' and not existing value
        $objects = $tagsTree->findByTags(['a' => 'notExistingValue']);
        $this->assertCount(0, $objects);
        $this->assertNotContains($object0, $objects);
        $this->assertNotContains($object1, $objects);
        $this->assertNotContains($object2, $objects);
        $this->assertNotContains($object3, $objects);
        $this->assertNotContains($object4, $objects);
        $this->assertNotContains($object5, $objects);
        
        // Test with 'a' and 'b'
        $objects = $tagsTree->findByTags(['a' => 'aValue', 'b' => 'bValue']);
        $this->assertCount(2, $objects);
        $this->assertNotContains($object0, $objects);
        $this->assertNotContains($object1, $objects);
        $this->assertNotContains($object2, $objects);
        $this->assertContains($object3, $objects);
        $this->assertContains($object4, $objects);
        $this->assertNotContains($object5, $objects);
    }
}
