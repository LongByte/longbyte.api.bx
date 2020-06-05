<?php

namespace Api\Core\Iblock\Iblock;

/**
 * Class \Api\Core\Iblock\Iblock\Model
 */
class Model extends \Api\Core\Base\Model {

    /**
     * @var int
     */
    protected static $_iblockId = 0;

    /**
     * 
     * @return string
     */
    public static function getTable() {
        return \Bitrix\Iblock\IblockTable::class;
    }

    /**
     * 
     * @return string
     */
    public static function getEntity() {
        return Entity::class;
    }

}
