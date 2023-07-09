<?php

namespace Api\Core\Base\Virtual;

/**
 * Class \Api\Core\Base\Virtual\Model
 */
abstract class Model extends \Api\Core\Base\Model
{
    public static function getTable(): string
    {
        return '';
    }

    public static function getOne(array $arFilter = array(), array $arParams = array()): ?Entity
    {
        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()): \Api\Core\Base\Collection
    {
        $strCollectionClass = static::getEntity()::getCollection();
        /** @var \Api\Core\Base\Collection $obCollection */
        $obCollection = new $strCollectionClass();
        return $obCollection;
    }

    public static function getFromArray(array $arItems): \Api\Core\Base\Collection
    {
        $strCollectionClass = static::getEntity()::getCollection();
        /** @var \Api\Core\Base\Collection $obCollection */
        $obCollection = new $strCollectionClass();

        foreach ($arItems as $arItem) {
            $obEntity = static::_getEntityFromItem($arItem);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    protected static function _getEntityFromItem(array $arItem): Entity
    {
        $strEntityClass = static::getEntity();
        $obEntity = new $strEntityClass($arItem);

        return $obEntity;
    }
}
