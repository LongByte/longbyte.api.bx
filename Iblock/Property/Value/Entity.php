<?php

namespace Api\Core\Iblock\Property\Value;

/**
 * Class \Api\Core\Iblock\Property\Value\Entity
 *
 * @method mixed getValue()
 * @method $this setValue(mixed $mixedValue)
 * @method bool hasValue()
 * @method mixed getValueXmlId()
 * @method $this setValueXmlId(mixed $mixedValueXmlId)
 * @method bool hasValueXmlId()
 * @method mixed getValueId()
 * @method $this setValueId(mixed $mixedValueId)
 * @method bool hasValueId()
 * @method mixed getDescription()
 * @method $this setDescription(mixed $mixedDescription)
 * @method bool hasDescription()
 */
class Entity extends \Api\Core\Base\Virtual\Entity
{
    protected static string $_primaryField = 'VALUE';

    public static function getModel(): string
    {
        return Model::class;
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public static function getFields(): array
    {
        $arFields = array(
            'VALUE',
            'VALUE_XML_ID',
            'VALUE_ID',
            'DESCRIPTION',
        );
        return $arFields;
    }
}
