<?php

namespace Api\Core\Iblock\Iblock;

/**
 * Class \Api\Core\Iblock\Iblock\Entity
 *
 */
class Entity extends \Api\Core\Base\Entity {

    protected static $arFields = array(
        'ID',
        'XML_ID',
        'IBLOCK_TYPE_ID',
        'CODE',
        'NAME',
        'SORT',
        'ACTIVE',
        'DESCRIPTION',
        'LIST_PAGE_URL',
        'DETAIL_PAGE_URL',
        'SECTION_PAGE_URL'
    );

    /**
     *
     * @var array
     */
    protected $_arIProperty = null;

    /**
     * 
     * @return string
     */
    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return array
     */
    public function getMeta() {
        if (is_null($this->_arIProperty)) {
            $obIProperty = new \Bitrix\Iblock\InheritedProperty\IblockValues($this->getId());
            $this->_arIProperty = $obIProperty->getValues();
        }
        return $this->_arIProperty;
    }

    /**
     * 
     * @return $this
     */
    public function setMeta() {
        $this->getMeta();

        \Api\Core\Main\Seo::getInstance()->setMeta(array(
            'page_title' => $this->_arIProperty['IBLOCK_PAGE_TITLE'],
            'meta_title' => $this->_arIProperty['IBLOCK_META_TITLE'],
            'meta_keywords' => $this->_arIProperty['IBLOCK_META_KEYWORDS'],
            'meta_description' => $this->_arIProperty['IBLOCK_META_DESCRIPTION'],
        ));
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function addToBreadcrumbs() {
        $this->getMeta();

        $strName = $this->_arIProperty['IBLOCK_PAGE_TITLE'] ?: $this->getName();
        $strUrl = $this->hasListPageUrl() ? $this->getListPageUrl() : '';
        \Api\Core\Main\Seo::getInstance()->addBreadcrumb($strName, $strUrl);
        return $this;
    }

}
