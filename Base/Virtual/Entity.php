<?php

namespace Api\Core\Base\Virtual;

/**
 * Class \Api\Core\Base\Virtual\Entity
 */
abstract class Entity extends \Api\Core\Base\Entity
{
    protected static string $_primaryField = 'ID';

    public function __construct(array $data = array())
    {
        $this->_data = array_fill_keys($this->getFields(), '');
        if ($data) {
            foreach ($data as $strField => $value) {
                if (array_key_exists($strField, $this->_data)) {
                    $this->_data[$strField] = $value;
                }
            }
        }
    }

    public function getData(): ?array
    {
        return null;
    }

    public function isExists(): bool
    {
        return true;
    }

    public function save(): self
    {
        return $this;
    }

    public function delete(): self
    {
        return $this;
    }

    public function getPrimaryField(): string
    {
        return static::$_primaryField;
    }
}
