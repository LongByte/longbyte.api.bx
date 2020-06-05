<?php

namespace Api\Core\Main\File;

/**
 * Class \Api\Core\Main\File\Model
 */
class Model extends \Api\Core\Base\Model {

    public static function getEntity() {
        return Entity::class;
    }

    public static function getTable() {
        return \Bitrix\Main\FileTable::class;
    }

}
