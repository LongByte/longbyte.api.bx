<?php

namespace Api\Core\Main;

use Bitrix\Main\Page\Asset;

/**
 * Class \Api\Core\Main\Seo
 *
 */
class Seo {

    /**
     * @var \Api\Core\Main\Seo
     */
    private static $instance;

    /**
     * @var string
     */
    private $_h1;

    /**
     * @var string
     */
    private $_title;

    /**
     * @var string
     */
    private $_description;

    /**
     * @var string
     */
    private $_keywords;

    /**
     * @var string
     */
    private $_prev;

    /**
     * @var string
     */
    private $_next;

    /**
     * @var string
     */
    private $_canonical;

    /**
     *
     * @var array
     */
    private $_breadcrumbs = array();

    protected function __construct() {
        
    }

    /**
     * 
     * @return \Api\Core\Main\Seo
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Self();
        }

        return self::$instance;
    }

    /**
     * @param null $arParams
     * @return $this
     */
    public function setMeta($arParams = null) {
        if (is_null($arParams)) {
            return $this;
        }
        if (strlen($arParams['meta_title']) > 0) {
            $this->_title = $arParams['meta_title'];
        }
        if (strlen($arParams['meta_keywords']) > 0) {
            $this->_keywords = $arParams['meta_keywords'];
        }
        if (strlen($arParams['meta_description']) > 0) {
            $this->_description = $arParams['meta_description'];
        }
        if (strlen($arParams['page_title']) > 0) {
            $this->_h1 = $arParams['page_title'];
        }
        if (strlen($arParams['next']) > 0) {
            $this->_next = $arParams['next'];
        }
        if (strlen($arParams['prev']) > 0) {
            $this->_prev = $arParams['prev'];
        }
        if (strlen($arParams['canonical']) > 0) {
            $this->_canonical = $arParams['canonical'];
        }

        return $this;
    }

    /**
     *
     * @global \CMain $APPLICATION
     */
    public function setMetaPage() {
        global $APPLICATION;
        if ($this->_title !== null) {
            $APPLICATION->SetPageProperty('title', $this->_title);
        }
        if ($this->_description !== null) {
            $APPLICATION->SetPageProperty('description', $this->_description);
        }
        if ($this->_keywords !== null) {
            $APPLICATION->SetPageProperty('keywords', $this->_keywords);
        }
        if ($this->_h1 !== null) {
            $APPLICATION->SetTitle($this->_h1);
        }
        if ($this->_next !== null) {
            Asset::getInstance()->addString('<link rel="prev" href="' . $this->_next . '"/>', true);
        }
        if ($this->_prev !== null) {
            Asset::getInstance()->addString('<link rel="next" href="' . $this->_prev . '"/>', true);
        }
        if ($this->_canonical !== null) {
            Asset::getInstance()->addString('<link rel="canonical" href="' . $this->_canonical . '"/>', true);
        }

        foreach ($this->_breadcrumbs as $arBreadcrumb) {
            $APPLICATION->AddChainItem($arBreadcrumb['name'], $arBreadcrumb['url']);
        }
    }

    public function getPageTitle() {
        return $this->_h1;
    }

    /**
     * 
     * @param string $strTitle
     * @return $this
     */
    public function setPageTitle($strTitle) {
        $this->_h1 = $strTitle;
        return $this;
    }

    /**
     * 
     * @param string $strName
     * @param string $strUrl
     * @return $this
     */
    public function addBreadcrumb($strName, $strUrl = '') {
        $this->_breadcrumbs[] = array(
            'name' => $strName,
            'url' => $strUrl,
        );
        return $this;
    }

    /**
     * @param array $arItems
     * @return $this
     */
    public function setBreadcrumbs(array $arItems) {
        $this->_breadcrumbs = $arItems;

        return $this;
    }

}
