<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Model
 */
abstract class Model extends \Api\Core\Base\Model {

    public static function getTable() {
        return \Api\Core\Iblock\Property\Table::class;
    }

}
