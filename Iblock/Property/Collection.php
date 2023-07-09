<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Collection
 */
class Collection extends \Api\Core\Base\Collection
{
    protected static string $_keyFunction = 'getCode';

    public function __call(string $name, mixed $arguments)
    {
        if ((strpos($name, "get") === 0)) {

            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $obEntity = $this->getByKey($strField);
            if (!is_null($obEntity)) {
                return $obEntity;
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } elseif ((strpos($name, "has") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $obEntity = $this->getByKey($strField);
            if (!is_null($obEntity)) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new \Exception("Call to undefined method {$name}");
        }
    }

    protected static function toLower(string $strString): string
    {
        return ToLower($strString);
    }

    protected static function toUpper(string $strString): string
    {
        return ToUpper($strString);
    }
}
