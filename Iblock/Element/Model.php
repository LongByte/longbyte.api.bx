<?php

namespace Api\Core\Iblock\Element;

use Bitrix\Main\Loader;

/**
 * Class \Api\Core\Iblock\Element\Model
 */
abstract class Model extends \Api\Core\Base\Model {

    /**
     * @var int
     */
    protected static $_iblockId = 0;

    /**
     * 
     * @return string
     */
    public static function getTable(): string {
        return '';
    }

    /**
     * 
     * @return int
     */
    public static function getIblockId(): int {
        return static::$_iblockId;
    }

    /**
     * 
     * @param array $arFilter
     * @return \Api\Core\Entity\Base|null
     */
    public static function getOne(array $arFilter = array()) {

        if ($obElement = static::_getElementObject($arFilter)) {

            $obEntity = static::_getEntityFromElementObject($obElement);
            return $obEntity;
        }

        return null;
    }

    /**
     * 
     * @param array $arFilter
     * @return array|null
     */
    public static function getOneAsArray(array $arFilter = array(), array $arParams = array()): ?array {

        if ($obElement = static::_getElementObject($arFilter, $arParams)) {

            $arElement = $obElement->GetFields();
            $arElement['PROPERTIES'] = $obElement->GetProperties();
            $arElement = static::_getFromTilda($arElement);

            return $arElement;
        }

        return null;
    }

    /**
     * 
     * @param array $arFilter
     * @param int $iLimit
     * @param int $iPageSize
     * @param int $iNumPage
     * @return \Api\Core\Base\Collection
     */
    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iPageSize = 0, int $iNumPage = 0) {

        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        $arSelect = static::getEntity()::getFields();
        $arSelect[] = 'IBLOCK_ID';

        $arNavigation = array();
        if ($iLimit > 0) {
            $arNavigation['nTopCount'] = $iLimit;
        }
        if ($iPageSize > 0) {
            $arNavigation['nPageSize'] = $iPageSize;
            if ($iNumPage > 0) {
                $arNavigation['iNumPage'] = $iNumPage;
            }
        }

        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        $rsElement = \CIBlockElement::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
                $arFilter,
                false,
                $arNavigation ?: false,
                $arSelect
        );

        while ($obElement = $rsElement->GetNextElement(false, true)) {

            $obEntity = static::_getEntityFromElementObject($obElement);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @param array $arElements
     * @return \Api\Core\Base\Collection
     */
    public static function getFromArray(array $arElements) {
        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        foreach ($arElements as $arElement) {
            $obEntity = static::_getEntityFromElementArray($arElement);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @param array $arFilter
     * @param array $arParams
     * @return \_CIBElement|array|false
     */
    protected static function _getElementObject(array $arFilter = array(), array $arParams = array()) {
        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        $arSelect = static::getEntity()::getFields();
        $arSelect[] = 'IBLOCK_ID';

        $rsElement = \CIBlockElement::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
                $arFilter,
                false,
                array('nTopCount' => 1),
                $arSelect
        );

        $obElement = $rsElement->GetNextElement(false, true);

        return $obElement;
    }

    /**
     * 
     * @param \_CIBElement $obElement
     * @return \Api\Core\Iblock\Element\Entity
     */
    protected static function _getEntityFromElementObject($obElement) {
        $arElement = $obElement->GetFields();
        $arProperties = $obElement->GetProperties();

        $arElement = static::_getFromTilda($arElement);
        $obEntity = static::_getEntityFromElementArray($arElement, $arProperties);

        return $obEntity;
    }

    /**
     * 
     * @param array $arElement
     * @param array|null $arProperties
     * @return \Api\Core\Iblock\Element\Entity
     */
    protected static function _getEntityFromElementArray(array $arElement, array $arProperties = null) {

        if (is_null($arProperties)) {
            $arProperties = $arElement['PROPERTIES'];
        }

        $strEntityClass = static::getEntity();
        /** @var \Api\Core\Iblock\Element\Entity $obEntity */
        $obEntity = new $strEntityClass($arElement['ID'], $arElement);
        $obPropertyCollection = $obEntity->getPropertyCollection();

        $arAllowProps = $obEntity->getProps();

        foreach ($arProperties as $arProperty) {
            if (!in_array($arProperty['CODE'], $arAllowProps)) {
                continue;
            }
            $obProperty = new \Api\Core\Iblock\Property\Entity($arProperty['ID'], $arProperty);
            $obPropertyCollection->addItem($obProperty);
        }

        return $obEntity;
    }

}
