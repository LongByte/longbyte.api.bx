<?php

namespace Api\Core\Iblock\Property\Value;

/**
 * Class \Api\Core\Iblock\Property\Value\Model
 */
class Model extends \Api\Core\Base\Virtual\Model
{

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
