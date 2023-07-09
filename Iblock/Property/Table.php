<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Table
 */
class Table extends \Bitrix\Iblock\PropertyTable
{
    public static function getScalarFields(): array
    {
        $arFields = array();
        foreach (static::getMap() as $strId => $obField) {
            if ($obField instanceof \Bitrix\Main\Entity\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }
}