<?php

namespace Api\Core\Iblock\Element\Tag;

/**
 * Class \Api\Core\Iblock\Element\Tag\Entity
 * 
 */
class Entity extends \Api\Core\Base\Entity {

    /**
     * 
     * @return null|array
     */
    public function getData() {

        return $this->_data;
    }

    public static function getModel() {
        return \Api\Core\Model\Base::class;
    }

}
