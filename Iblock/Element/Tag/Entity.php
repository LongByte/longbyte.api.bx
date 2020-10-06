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
    public function getData(): ?array {
        return $this->_data;
    }

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return \Api\Core\Model\Base::class;
    }

}
