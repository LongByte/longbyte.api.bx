<?php

namespace Api\Core\Base;

/**
 * Class \Api\Core\Base\Collection
 *
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {

    protected static $_keyFunction = 'getId';
    protected $_uniqueMode = false;
    protected $_collection = array();
    protected $_keys = array();

    /**
     * 
     * @param \Api\Core\Base\Entity $obEntity
     * @return $this
     */
    public function addItem($obEntity) {
        if ($obEntity instanceof \Api\Core\Base\Virtual\Entity) {
            $strGetFunction = 'get' . \Api\Core\Utils\StringHelper::convertCodeToUpperCamelCase($obEntity->getPrimaryField());
        } else {
            $strGetFunction = static::$_keyFunction;
        }
        if (!$this->_uniqueMode || $this->_uniqueMode && !$this->getByKey($obEntity->$strGetFunction())) {
            $this->_collection[] = $obEntity;
            $this->_keys[] = $obEntity->$strGetFunction();
        }
        return $this;
    }

    public function getCollection() {
        return $this->_collection;
    }

    /**
     * 
     * @param bool $bUniqueMode
     * @return $this
     */
    public function setUniqueMode(bool $bUniqueMode) {
        $this->_uniqueMode = $bUniqueMode;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getKeys() {
        return array_values($this->_keys);
    }

    /**
     *
     * @param string $strKey
     * @return \Api\Core\Base\Entity
     */
    public function getByKey($strKey) {
        $iCollectionKey = array_search($strKey, $this->_keys);
        if ($iCollectionKey !== false) {
            return $this->_collection[$iCollectionKey];
        }
        return null;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->_collection);
    }

    /**
     * Whether a offset exists
     */
    public function offsetExists($offset) {
        return isset($this->_collection[$offset]) || array_key_exists($offset, $this->collection);
    }

    /**
     * Offset to retrieve
     */
    public function offsetGet($offset) {
        if (isset($this->_collection[$offset]) || array_key_exists($offset, $this->collection)) {
            return $this->_collection[$offset];
        }

        return null;
    }

    /**
     * Offset to set
     */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            $this->_collection[] = $value;
        } else {
            $this->_collection[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     */
    public function offsetUnset($offset) {
        unset($this->_collection[$offset]);
    }

    /**
     * Count elements of an object
     */
    public function count() {
        return count($this->_collection);
    }

    /**
     * Return the current element
     */
    public function current() {
        return current($this->_collection);
    }

    /**
     * Move forward to next element
     */
    public function next() {
        return next($this->_collection);
    }

    /**
     * Return the key of the current element
     */
    public function key() {
        return key($this->_collection);
    }

    /**
     * Checks if current position is valid
     */
    public function valid() {
        $key = $this->key();
        return $key !== null;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() {
        return reset($this->_collection);
    }

    /**
     * Checks if collection is empty.
     *
     * @return bool
     */
    public function isEmpty() {
        return empty($this->_collection);
    }

    /**
     *
     * @return array
     */
    public function toArray() {
        $arArray = array();
        foreach ($this->_collection as $obItem) {
            $arItem = $obItem->toArray();
            $arArray[] = $arItem;
        }

        return $arArray;
    }

}
