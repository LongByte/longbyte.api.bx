<?php

namespace Api\Core\Base\Virtual;

/**
 * Class \Api\Core\Base\Virtual\Model
 */
abstract class Model extends \Api\Core\Base\Model {

    public static function getTable() {
        return null;
    }

    public static function getOne() {
        return null;
    }

    public static function getAll() {
        return null;
    }

    /**
     * 
     * @param array $arItems
     * @return \Api\Core\Base\Collection
     */
    public static function getFromArray(array $arItems) {

        $strCollectionClass = static::getEntity()::getCollection();
        /** @var \Api\Core\Base\Collection $obCollection */
        $obCollection = new $strCollectionClass();

        foreach ($arItems as $arItem) {
            $obEntity = static::_getEntityFromItem($arItem);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @param array $arItem
     * @return \Api\Core\Base\Virtual\Entity
     */
    protected static function _getEntityFromItem($arItem) {

        $strEntityClass = static::getEntity();
        $obEntity = new $strEntityClass($arItem);

        return $obEntity;
    }

}
