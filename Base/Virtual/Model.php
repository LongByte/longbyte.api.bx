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

    public static function getOne()
    {
        return null;
    }

    public static function getAll()
    {
        $strCollectionClass = static::getEntity()::getCollection();
        /** @var \Api\Core\Base\Collection $obCollection */
        $obCollection = new $strCollectionClass();
        return $obCollection;
    }

    public static function getFromArray(array $arItems)
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

    protected static function _getEntityFromItem($arItem): Entity
    {
        $strEntityClass = static::getEntity();
        $obEntity = new $strEntityClass($arItem);

        return $obEntity;
    }

}
