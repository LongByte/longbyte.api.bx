<?php

namespace Api\Core\Base\Virtual;

/**
 * Class \Api\Core\Base\Virtual\Entity
 *
 */
abstract class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var string
     */
    protected static $_primaryField = 'ID';

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if ($data) {
            $this->_data = array_fill_keys($this->getFields(), '');
            foreach ($data as $strField => $value) {
                if (array_key_exists($strField, $this->_data)) {
                    $this->_data[$strField] = $value;
                }
            }
        }
    }

    public function getData(): ?array {
        return null;
    }

    /**
     * @return bool
     */
    public function isExists(): bool {
        return true;
    }

    /**
     * 
     * @return null
     */
    public function save() {
        return null;
    }

    /**
     * 
     * @return null
     */
    public function delete() {
        return null;
    }

    /**
     * 
     * @return string
     */
    public function getPrimaryField(): string {
        return static::$_primaryField;
    }

}
