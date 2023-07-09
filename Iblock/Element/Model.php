<?php

namespace Api\Core\Iblock\Element;

use Bitrix\Main\Loader;

/**
 * Class \Api\Core\Iblock\Element\Model
 */
abstract class Model extends \Api\Core\Base\Model
{
    protected static int $_iblockId = 0;

    public static function getTable(): string
    {
        return '';
    }

    public static function getIblockId(): int
    {
        return static::$_iblockId;
    }

    public static function getOne(array $arFilter = array(), array $arParams = array()): ?Entity
    {
        if ($obElement = static::_getElementObject($arFilter)) {

            $obEntity = static::_getEntityFromElementObject($obElement);
            return $obEntity;
        }

        return null;
    }

    public static function getOneAsArray(array $arFilter = array(), array $arParams = array()): ?array
    {
        if ($obElement = static::_getElementObject($arFilter, $arParams)) {

            $arElement = $obElement->GetFields();
            $arElement['PROPERTIES'] = $obElement->GetProperties();
            $arElement = static::_getFromTilda($arElement);

            return $arElement;
        }

        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()): \Api\Core\Base\Collection
    {
        throw new \Exception('Use getAllElements method');
    }

    public static function getAllElements(array $arFilter = array(), int $iLimit = 0, int $iPageSize = 0, int $iNumPage = 0): \Api\Core\Base\Collection
    {
        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        /** @var Entity $obEntityClass */
        $obEntityClass = static::getEntity();
        $arSelect = $obEntityClass::getFields();

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

        $strCollectionClass = $obEntityClass::getCollection();
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

    public static function getFromArray(array $arElements): \Api\Core\Base\Collection
    {
        /** @var Entity $obEntityClass */
        $obEntityClass = static::getEntity();
        $strCollectionClass = $obEntityClass::getCollection();
        $obCollection = new $strCollectionClass();

        foreach ($arElements as $arElement) {
            $obEntity = static::_getEntityFromElementArray($arElement);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    protected static function _getElementObject(array $arFilter = array(), array $arParams = array()): \_CIBElement|array|false
    {
        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        /** @var Entity $obEntityClass */
        $obEntityClass = static::getEntity();
        $arSelect = $obEntityClass::getFields();
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

    protected static function _getEntityFromElementObject(\_CIBElement $obElement): Entity
    {
        $arElement = $obElement->GetFields();
        $arProperties = $obElement->GetProperties();

        $arElement = static::_getFromTilda($arElement);
        $obEntity = static::_getEntityFromElementArray($arElement, $arProperties);

        return $obEntity;
    }

    protected static function _getEntityFromElementArray(array $arElement, array $arProperties = null): Entity
    {

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
