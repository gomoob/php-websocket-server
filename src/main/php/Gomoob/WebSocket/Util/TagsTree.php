<?php

/**
 * gomoob/php-websocket-server
 *
 * @copyright Copyright (c) 2016, GOMOOB SARL (http://gomoob.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE.md file)
 */
namespace Gomoob\WebSocket\Util;

/**
 * Data structure used to map `{tagName,tagValue}` to general PHP objects, this is like an in memory database.
 *
 * @author Baptiste Gaillard (baptiste.gaillard@gomoob.com)
 */
class TagsTree
{
    /**
     * A map used to associated general PHP objects to their associated `{tagName,tagValue}`.
     *
     * @var array
     */
    protected $objectsToTagsMap = [];
    
    /**
     * A structure which stores all the references to added objects.
     *
     * @var \SplObjectStorage
     */
    protected $objects = null;

    /**
     * A map used to associate `{tagName,tagValue}` to general PHP objects.
     *
     * @var array
     */
    protected $tagsObjectsMap = [];
    
    /**
     * Creates a new `TagsTree` instance.
     */
    public function __construct()
    {
        $this->objects = new \SplObjectStorage();
    }
    
    /**
     * Function used to add a general PHP object with its corresponding tags.
     *
     * @param object $object the general PHP object to add.
     * @param array $tags the associated tags, this must be an array with only `string => int` or `string => string`
     *        mappings.
     */
    public function add($object, array $tags = [])
    {
        // Validates the 'tags'
        $this->validateTags($tags);
        
        // Adds the object
        $this->objects->attach($object);
        
        // Computes the object hash
        $hash = $this->objects->getHash($object);
        
        // Stores the object tags
        $this->objectsToTagsMap[$hash] = $tags;
        
        // Stores the mappings in the tags => objects map
        foreach ($tags as $tagName => $tagValue) {
            // Reserves an array for the tag name
            if (!array_key_exists($tagName, $this->tagsObjectsMap)) {
                $this->tagsObjectsMap[$tagName] = [];
            }
            
            // Reserves an array for the tag value
            if (!array_key_exists($tagValue, $this->tagsObjectsMap[$tagName])) {
                $this->tagsObjectsMap[$tagName][$tagValue] = [];
            }

            // Stores the tags => object mapping
            $this->tagsObjectsMap[$tagName][$tagValue][$hash] = $object;
        }
    }
    
    /**
     * Indicates if the structures contains the object.
     *
     * @param object $object the object used to do the check.
     *
     * @return boolean `true` if the structure contains the object, `false` otherwise.
     */
    public function contains($object)
    {
        return $this->objects->contains($object);
    }
    
    /**
     * Counts the total number of objects stored.
     *
     * @return int the total number of objects stored.
     */
    public function count()
    {
        return $this->objects->count();
    }
    
    /**
     * Remove an object.
     *
     * @param object $object the object to remove.
     */
    public function delete($object)
    {
        if ($this->contains($object)) {
            // Computes the object hash
            $hash = $this->objects->getHash($object);
            
            // Clear the tags => objects map
            foreach ($this->objectsToTagsMap[$hash] as $tagName => $tagValue) {
                unset($this->tagsObjectsMap[$tagName][$tagValue][$hash]);
                
                if (empty($this->tagsObjectsMap[$tagName][$tagValue])) {
                    unset($this->tagsObjectsMap[$tagName][$tagValue]);
                }
                if (empty($this->tagsObjectsMap[$tagName])) {
                    unset($this->tagsObjectsMap[$tagName]);
                }
            }
            
            // Clear the objects => tags map
            unset($this->objectsToTagsMap[$hash]);
            
            // Remove the object
            $this->objects->detach($object);
        }
    }
    
    /**
     * Function used to find objects which match specified tags.
     *
     * @param array $tags the tags used to do the search.
     *
     * @return object[] the found objects.
     */
    public function findByTags(array $tags = [])
    {
        $objects = [];
        
        // If no tags are provided we return all the objects
        if (count($tags) === 0) {
            foreach ($this->objects as $object) {
                $objects[$this->objects->getHash($object)] = $object;
            }
        } // Otherwise we filter the objects
        else {
            foreach ($tags as $tagName => $tagValue) {
                if (array_key_exists($tagName, $this->tagsObjectsMap) &&
                   array_key_exists($tagValue, $this->tagsObjectsMap[$tagName])) {
                    if (empty($objects)) {
                        $objects = $this->tagsObjectsMap[$tagName][$tagValue];
                    } else {
                        $objects = array_intersect_key($objects, $this->tagsObjectsMap[$tagName][$tagValue]);
                    }
                }
            }
        }
        
        return $objects;
    }
    
    /**
     * Resets the tags tree.
     */
    public function reset()
    {
        unset($this->objectsToTagsMap);
        unset($this->tagsObjectsMap);
    
        $this->objects = new \SplObjectStorage();
        $this->objectsToTagsMap = [];
        $this->tagsObjectsMap = [];
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
