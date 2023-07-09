<?php

namespace Api\Core\Base;

use Api\Core\Utils\ArrayHelper;

/**
 * Class \Api\Core\Base\Model
 */
abstract class Model
{
    abstract public static function getTable(): string;

    abstract public static function getEntity(): string;

    public static function getOne(array $arFilter = array(), array $arParams = array()): ?Entity
    {

        $arRow = static::getOneAsArray($arFilter, $arParams);

        if (!is_null($arRow)) {

            $primaryField = static::getTable()::getEntity()->getPrimary();
            if (is_array($primaryField)) {
                foreach ($primaryField as $strField) {
                    $primary[$strField] = array_key_exists($strField, $arRow) ? $arRow[$strField] : null;
                }
            } else {
                $primary = $arRow[$primaryField];
            }

            $strEntityClass = static::getEntity();
            $obEntity = new $strEntityClass($primary, $arRow);
            return $obEntity;
        }

        return null;
    }

    public static function getOneAsArray(array $arFilter = array(), array $arParams = array()): ?array
    {
        $arParams['filter'] = $arFilter;
        if (!ArrayHelper::keyExists('select', $arParams)) {
            $arParams['select'] = static::getEntity()::getFields();
        }
        $arRow = static::getTable()::getRow($arParams);

        if ($arRow) {
            return $arRow;
        }

        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()): Collection
    {

        $arRows = static::getAllAsArray($arFilter, $iLimit, $iOffset, $arParams);
        $primaryField = static::getTable()::getEntity()->getPrimary();

        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        foreach ($arRows as $arRow) {
            if (is_array($primaryField)) {
                foreach ($primaryField as $strField) {
                    $primary[$strField] = array_key_exists($strField, $arRow) ? $arRow[$strField] : null;
                }
            } else {
                $primary = $arRow[$primaryField];
            }

            $strEntityClass = static::getEntity();
            $obEntity = new $strEntityClass($primary, $arRow);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    public static function getAllAsArray(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()): array
    {
        $arParams['filter'] = $arFilter;
        if ($iLimit > 0) {
            $arParams['limit'] = $iLimit;
        }
        if ($iOffset > 0) {
            $arParams['offset'] = $iOffset;
        }
        if (!ArrayHelper::keyExists('select', $arParams)) {
            $arParams['select'] = static::getEntity()::getFields();
        }

        $arRows = static::getTable()::getList($arParams)->fetchAll();

        return $arRows;
    }

    protected static function _getFromTilda(array $array): array
    {
        $clearArray = array();
        foreach ($array as $strKey => $value) {
            if (strpos($strKey, '~') === 0) {
                continue;
            }

            $hasTilda = array_key_exists('~' . $strKey, $array);
            if ($hasTilda) {
                $clearArray[$strKey] = $array['~' . $strKey];
            } else {
                $clearArray[$strKey] = $value;
            }
        }
        return $clearArray;
    }

}
