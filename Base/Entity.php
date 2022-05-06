<?php

namespace Api\Core\Base;

/**
 * Class \Api\Core\Base\Entity
 */
abstract class Entity
{

    /** @var array|int */
    protected $_primary;
    protected bool $_exists = false;
    protected bool $_changed = false;
    protected ?array $_data = null;
    protected ?\Bitrix\Main\Result $_obDBResult = null;
    protected static array $arFields = array('ID');

    abstract public static function getModel(): string;

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public function __construct($primary = null, array $data = array())
    {
        if ($data) {
            $this->_data = array_fill_keys($this->getFields(), '');
            foreach ($data as $strField => $value) {
                $this->_data[$strField] = $value;
            }
            if ($primary === null) {
                if (!is_null(static::getModel()::getTable())) {
                    $primaryField = static::getModel()::getTable()::getEntity()->getPrimary();
                    if (is_array($primaryField)) {
                        foreach ($primaryField as $strField) {
                            $primary[$strField] = array_key_exists($strField, $data) ? $data[$strField] : null;
                        }
                    } else {
                        $primary = $data[$primaryField];
                    }
                }
            }
            if ($primary !== null) {
                $this->_primary = $primary;
                $this->_exists = true;
            }
        } elseif ($primary !== null) {
            $this->_primary = $primary;
            $this->getData();
        } else {
            $this->getData();
        }
    }

    public function getData(): ?array
    {
        if (is_null($this->_data)) {
            $this->_data = array_fill_keys($this->getFields(), '');

            if (!is_null($this->_primary)) {
                $primaryField = static::getModel()::getTable()::getEntity()->getPrimary();
                if (is_array($primaryField)) {
                    $arPrimaryFilter = $this->_primary;
                } else {
                    $arPrimaryFilter = array($primaryField => $this->_primary);
                }

                if ($arPrimaryFilter !== null) {
                    $_arData = static::getModel()::getTable()::getRow(array(
                        'filter' => $arPrimaryFilter,
                        'select' => $this->getFields(),
                    ));
                    if ($_arData) {
                        foreach ($_arData as $strField => $value) {
                            if (array_key_exists($strField, $this->_data)) {
                                $this->_data[$strField] = $value;
                            }
                        }
                        $this->_exists = true;
                    }
                }
            }
        }

        return $this->_data;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        if ((strpos($name, "get") === 0)) {

            $strKey = substr_replace($name, "", 0, 3);
            if (strlen($strKey) > 0 && count($arguments) == 0) {
                preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
                $arField = array();
                foreach ($res as $arRes) {
                    $arField[] = $arRes[0];
                }
                $strField = self::toUpper(implode('_', $arField));
            } else {
                $strField = $arguments[0];
                $value = $arguments[1];
            }
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return $arData[$strField];
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } elseif ((strpos($name, "has") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            if (strlen($strKey) > 0 && count($arguments) == 0) {
                preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
                $arField = array();
                foreach ($res as $arRes) {
                    $arField[] = $arRes[0];
                }
                $strField = self::toUpper(implode('_', $arField));
            } else {
                $strField = $arguments[0];
                $value = $arguments[1];
            }
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return true;
            } else {
                return false;
            }
        } elseif ((strpos($name, "set") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            if (strlen($strKey) > 0 && count($arguments) == 1) {
                preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
                $arField = array();
                foreach ($res as $arRes) {
                    $arField[] = $arRes[0];
                }
                $strField = self::toUpper(implode('_', $arField));
                $value = $arguments[0];
            } else {
                $strField = $arguments[0];
                $value = $arguments[1];
            }
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                if ($this->checkChanges($this->_data[$strField], $value)) {
                    $this->_changed = true;
                }
                $this->_data[$strField] = $value;
                return $this;
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } else {
            throw new \Exception("Call to undefined method {$name}");
        }
    }

    public function isExists(): bool
    {
        return $this->_exists;
    }

    public function isChanged(): bool
    {
        return $this->_changed;
    }

    /**
     *
     * @param mixed $oldValue
     * @param mixed $newValue
     * @return bool
     */
    protected function checkChanges($oldValue, $newValue): bool
    {
        if (is_null($oldValue) && !is_null($newValue) || !is_null($oldValue) && is_null($newValue)) {
            return true;
        }

        if ($oldValue instanceof \Bitrix\Main\Type\DateTime && $newValue instanceof \Bitrix\Main\Type\DateTime) {
            $oldValue = $oldValue->getTimestamp();
            $newValue = $newValue->getTimestamp();
        }

        if (is_numeric($oldValue) && is_numeric($newValue)) {
            $oldValue = (float) $oldValue;
            $newValue = (float) $newValue;
            if ($newValue != 0) {
                return abs(($oldValue - $newValue) / $newValue) > 0.000000001;
            }
        }

        return $oldValue != $newValue;
    }

    public function toArray($arData = null): array
    {
        if (is_null($arData)) {
            $arData = $this->_data;
        }
        $arArray = array();
        foreach ($arData as $strKey => $value) {
            if (strpos($strKey, '~') === 0) {
                continue;
            }
            $strLowerKey = self::toLower($strKey);
            if (is_array($value)) {
                $arArray[$strLowerKey] = self::toArray($value);
            } else {
                $arArray[$strLowerKey] = $value;
            }
        }

        return $arArray;
    }

    protected static function toLower(string $strString): string
    {
        return \ToLower($strString);
    }

    protected static function toUpper(string $strString): string
    {
        return \ToUpper($strString);
    }

    public function getFields(): array
    {
        return static::$arFields;
    }

    public function getDBResult(): ?\Bitrix\Main\Result
    {
        return $this->_obDBResult;
    }

    public function save(): self
    {
        $arFields = array();
        $arData = $this->getData();
        foreach ($this->getFields() as $strTableField) {
            if (array_key_exists($strTableField, $arData)) {
                $arFields[$strTableField] = $arData[$strTableField];
            }
        }
        if ($this->isExists()) {
            $rsResult = static::getModel()::getTable()::update($this->_primary, $arFields);
        } else {
            $rsResult = static::getModel()::getTable()::add($arFields);
            if (intval($rsResult->getId()) > 0) {
                $this->_primary = $rsResult->getId();
                $this->_exists = true;
            }
        }
        $this->_changed = false;
        $this->_obDBResult = $rsResult;

        return $this;
    }

    public function delete(): self
    {
        if ($this->isExists()) {
            $rsResult = static::getModel()::getTable()::delete($this->_primary);
            $this->_exists = false;
            $this->_primary = null;
            $this->_changed = true;
            $this->_obDBResult = $rsResult;
        }

        return $this;
    }

}
