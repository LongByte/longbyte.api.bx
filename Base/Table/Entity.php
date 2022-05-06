<?php

namespace Api\Core\Base\Table;

/**
 * Class \Api\Core\Base\Table\Entity
 */
abstract class Entity extends \Api\Core\Base\Entity
{

    public function getFields(): array
    {
        $arFields = array();
        /** @var \Bitrix\Main\ORM\Fields\Field $obField */
        foreach (static::getModel()::getTable()::getMap() as $keyField => $obField) {
            if ($obField instanceof \Bitrix\Main\ORM\Fields\ScalarField) {
                if (is_numeric($keyField)) {
                    $arFields[] = $obField->getColumnName();
                } else {
                    $arFields[] = $keyField;
                }
            }
        }
        return $arFields;
    }

}
