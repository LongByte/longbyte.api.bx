<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Model
 */
class Model extends \Api\Core\Base\Model {

    /**
     * 
     * @return string
     */
    public static function getTable(): string {
        return \Bitrix\Iblock\PropertyTable::class;
    }

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
