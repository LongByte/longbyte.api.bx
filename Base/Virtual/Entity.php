<?php

namespace Api\Core\Base\Virtual;

/**
 * Class \Api\Core\Base\Virtual\Entity
 *
 */
abstract class Entity extends \Api\Core\Base\Entity {

    protected static $_primaryField = 'ID';

    /**
     * DataEntity constructor.
     * @param array $data
     */
    public function __construct($data = array()) {
        if ($data) {
            $this->_data = array_fill_keys($this->getFields(), '');
            foreach ($data as $strField => $value) {
                if (array_key_exists($strField, $this->_data)) {
                    $this->_data[$strField] = $value;
                }
            }
        }
    }

    public function getData() {
        return null;
    }

    /**
     * @return bool
     */
    public function isExists() {
        return true;
    }

    public function save() {
        return null;
    }

    public function delete() {
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getPrimaryField() {
        return static::$_primaryField;
    }

}
