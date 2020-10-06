<?php

namespace Api\Core\Iblock\Section;

/**
 * Class \Api\Core\Iblock\Section\Entity

 */
abstract class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var \Api\Core\Main\File\Entity
     */
    protected $_obPicture = null;

    /**
     *
     * @var \Api\Core\Main\File\Entity 
     */
    protected $_obDetailPicture = null;

    /**
     *
     * @var array
     */
    protected $_arIProperty = null;

    /**
     * 
     * @return \Api\Core\Main\File\Entity
     */
    public function getPictureFile(): ?\Api\Core\Main\File\Entity {
        $iFile = 0;
        if (is_null($this->_obPicture)) {
            if ($this->hasPicture()) {
                $iFile = $this->getPicture();
            }
            $this->_obPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obPicture;
    }

    /**
     * 
     * @return \Api\Core\Main\File\Entity
     */
    public function getDetailPictureFile(): ?\Api\Core\Main\File\Entity {
        $iFile = 0;
        if (is_null($this->_obDetailPicture)) {
            if ($this->hasDetailPicture()) {
                $iFile = $this->getDetailPicture();
            }
            $this->_obDetailPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obDetailPicture;
    }

    /**
     * @return string
     */
    public function getSectionPageUrl(): string {
        $arReplaceFrom = array('#SITE_DIR#');
        $arReplaceTo = array('/');
        if ($this->hasCode()) {
            $arReplaceFrom[] = '#CODE#';
            $arReplaceTo[] = $this->getCode();
            $arReplaceFrom[] = '#SECTION_CODE#';
            $arReplaceTo[] = $this->getCode();
        }
        if ($this->hasId()) {
            $arReplaceFrom[] = '#ID#';
            $arReplaceTo[] = $this->getId();
            $arReplaceFrom[] = '#SECTION_ID#';
            $arReplaceTo[] = $this->getId();
        }
        if ($this->hasSectionCodePath()) {
            $arReplaceFrom[] = '#SECTION_CODE_PATH#';
            $arReplaceTo[] = $this->getSectionCodePath();
        }
        $url = str_replace($arReplaceFrom, $arReplaceTo, $this->getUrlTemplate());

        return preg_replace("'(?<!:)/+'s", "/", $url);
    }

    /**
     * 
     * @return string|null
     */
    public function getUrlTemplate(): ?string {
        if (is_null($this->_url_template)) {
            if ($obIblock = $this->getIblock()) {
                $this->_url_template = $obIblock->getSectionPageUrl();
            }
        }

        return $this->_url_template;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Iblock\Entity|null
     */
    public function getIblock(): ?\Api\Core\Iblock\Iblock\Entity {
        if (is_null($this->_iblock)) {
            $this->_iblock = \Api\Core\Iblock\Iblock\Model::getOne(array('ID' => static::getModel()::getIblockId()));
        }

        return $this->_iblock;
    }

    /**
     * 
     * @return array
     */
    public function getMeta(): array {
        if (is_null($this->_arIProperty)) {
            $obIProperty = new \Bitrix\Iblock\InheritedProperty\ElementValues(static::getModel()::getIblockId(), $this->getId());
            $this->_arIProperty = $obIProperty->getValues();
        }
        return $this->_arIProperty;
    }

    /**
     * 
     * @return \self
     */
    public function setMeta(): self {
        $this->getMeta();

        \Api\Core\Main\Seo::getInstance()->setMeta(array(
            'page_title' => $this->_arIProperty['SECTION_PAGE_TITLE'],
            'meta_title' => $this->_arIProperty['SECTION_META_TITLE'],
            'meta_keywords' => $this->_arIProperty['SECTION_META_KEYWORDS'],
            'meta_description' => $this->_arIProperty['SECTION_META_DESCRIPTION'],
        ));
        return $this;
    }

    /**
     * 
     * @return \self
     */
    public function addToBreadcrumbs(): self {
        $this->getMeta();

        $strName = $this->_arIProperty['SECTION_PAGE_TITLE'] ?: $this->getName();
        $strUrl = $this->getSectionPageUrl();
        \Api\Core\Main\Seo::getInstance()->addBreadcrumb($strName, $strUrl);
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function save() {
        $arData = array();
        foreach ($this->getFields() as $strField) {
            $arData[$strField] = $this->_data[$strField];
        }
        unset($arData['ID']);
        $arData['IBLOCK_ID'] = static::getIblockId();
        $obSection = new \CIBlockSection();
        if ($this->isExists()) {
            $obSection->Update($this->getId(), $arData);
            $this->_data = null;
            $this->getData();
        } else {
            if ($iId = $obSection->Add($arData)) {
                $this->setId($iId);
                $this->_primary = $iId;
            }
        }

        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function delete() {
        if ($this->isExists()) {
            $iId = $this->getId();
            \CIBlockSection::Delete($iId);
            $this->setId(0);
            $this->_primary = null;
            $this->_exists = false;
            $this->_changed = true;
        }
        return $this;
    }

}
