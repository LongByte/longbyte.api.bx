<?php

namespace Api\Core\Main\File;

/**
 * Class \Api\Core\Main\File\Model
 */
class Model extends \Api\Core\Base\Model
{

    public static function getEntity(): string
    {
        return Entity::class;
    }

    public static function getTable(): string
    {
        return \Bitrix\Main\FileTable::class;
    }

}
