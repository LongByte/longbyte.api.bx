<?php

namespace Api\Core\Iblock\Iblock;

/**
 * Class \Api\Core\Iblock\Iblock\Model
 */
class Model extends \Api\Core\Base\Model
{

    protected static int $_iblockId = 0;

    public static function getTable(): string
    {
        return \Bitrix\Iblock\IblockTable::class;
    }

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
