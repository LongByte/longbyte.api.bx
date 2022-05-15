<?php

namespace Api\Core\Base;

/**
 * Class \Api\Core\Base\Collection
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{

    protected static string $_keyFunction = 'getId';
    protected bool $_uniqueMode = false;
    protected array $_collection = array();
    protected array $_keys = array();

    public function addItem(\Api\Core\Base\Entity $obEntity): self
    {
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

    public function getCollection(): array
    {
        return $this->_collection;
    }

    public function setUniqueMode(bool $bUniqueMode): self
    {
        $this->_uniqueMode = $bUniqueMode;
        return $this;
    }

    public function getKeys(): array
    {
        return array_values($this->_keys);
    }

    public function getByKey($strKey): ?Entity
    {
        $iCollectionKey = array_search($strKey, $this->_keys);
        if ($iCollectionKey !== false) {
            return $this->_collection[$iCollectionKey];
        }
        return null;
    }

    public function removeByKey($strKey): self
    {
        $iCollectionKey = array_search($strKey, $this->_keys);
        if ($iCollectionKey !== false) {
            unset($this->_collection[$iCollectionKey]);
            unset($this->_keys[$iCollectionKey]);
        }
        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_collection);
    }

    /**
     * Whether a offset exists
     */
    public function offsetExists($offset)
    {
        return isset($this->_collection[$offset]) || array_key_exists($offset, $this->collection);
    }

    /**
     * Offset to retrieve
     */
    public function offsetGet($offset)
    {
        if (isset($this->_collection[$offset]) || array_key_exists($offset, $this->collection)) {
            return $this->_collection[$offset];
        }

        return null;
    }

    /**
     * Offset to set
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->_collection[] = $value;
        } else {
            $this->_collection[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     */
    public function offsetUnset($offset)
    {
        unset($this->_collection[$offset]);
    }

    /**
     * Count elements of an object
     */
    public function count()
    {
        return count($this->_collection);
    }

    /**
     * Return the current element
     */
    public function current()
    {
        return current($this->_collection);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        return next($this->_collection);
    }

    /**
     * Move forward to end element
     */
    public function end()
    {
        return end($this->_collection);
    }

    /**
     * Return the key of the current element
     */
    public function key()
    {
        return key($this->_collection);
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        $key = $this->key();
        return $key !== null;
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        return reset($this->_collection);
    }

    /**
     * Checks if collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->_collection);
    }

    public function clear(): self
    {
        $this->_collection = array();
        $this->_keys = array();
        return $this;
    }

    public function toArray(): array
    {
        $arArray = array();
        foreach ($this->_collection as $obItem) {
            $arItem = $obItem->toArray();
            $arArray[] = $arItem;
        }

        return $arArray;
    }

}
