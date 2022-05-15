<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Model
 */
class Model extends \Api\Core\Base\Model
{

    public static function getTable(): string
    {
        return Table::class;
    }

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
