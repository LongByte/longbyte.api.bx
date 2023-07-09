<?php

namespace Api\Core\Iblock\Section;

/**
 * Class \Api\Core\Iblock\Section\Entity
 */
abstract class Entity extends \Api\Core\Base\Entity
{
    protected ?\Api\Core\Main\File\Entity $_obPicture = null;
    protected ?\Api\Core\Main\File\Entity $_obDetailPicture = null;
    protected ?\Api\Core\Iblock\Iblock\Entity $_iblock = null;
    protected ?array $_arIProperty = null;

    public function getPictureFile(): ?\Api\Core\Main\File\Entity
    {
        $iFile = 0;
        if (is_null($this->_obPicture)) {
            if ($this->hasPicture()) {
                $iFile = $this->getPicture();
            }
            $this->_obPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obPicture;
    }

    public function getDetailPictureFile(): ?\Api\Core\Main\File\Entity
    {
        $iFile = 0;
        if (is_null($this->_obDetailPicture)) {
            if ($this->hasDetailPicture()) {
                $iFile = $this->getDetailPicture();
            }
            $this->_obDetailPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obDetailPicture;
    }

    public function getSectionPageUrl(): string
    {
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

    public function getUrlTemplate(): ?string
    {
        if (is_null($this->_url_template)) {
            if ($obIblock = $this->getIblock()) {
                $this->_url_template = $obIblock->getSectionPageUrl();
            }
        }

        return $this->_url_template;
    }

    public function getIblock(): ?\Api\Core\Iblock\Iblock\Entity
    {
        if (is_null($this->_iblock)) {
            /** @var \Api\Core\Iblock\Iblock\Entity $obIblock */
            $obIblock = \Api\Core\Iblock\Iblock\Model::getOne(array('ID' => static::getModel()::getIblockId()));
            $this->_iblock = $obIblock;
        }
        return $this->_iblock;
    }

    public function getMeta(): array
    {
        if (is_null($this->_arIProperty)) {
            $obIProperty = new \Bitrix\Iblock\InheritedProperty\ElementValues(static::getModel()::getIblockId(), $this->getId());
            $this->_arIProperty = $obIProperty->getValues();
        }
        return $this->_arIProperty;
    }

    public function setMeta(): self
    {
        $this->getMeta();

        \Api\Core\Main\Seo::getInstance()->setMeta(array(
            'page_title' => $this->_arIProperty['SECTION_PAGE_TITLE'],
            'meta_title' => $this->_arIProperty['SECTION_META_TITLE'],
            'meta_keywords' => $this->_arIProperty['SECTION_META_KEYWORDS'],
            'meta_description' => $this->_arIProperty['SECTION_META_DESCRIPTION'],
        ));
        return $this;
    }

    public function addToBreadcrumbs(): self
    {
        $this->getMeta();

        $strName = $this->_arIProperty['SECTION_PAGE_TITLE'] ?: $this->getName();
        $strUrl = $this->getSectionPageUrl();
        \Api\Core\Main\Seo::getInstance()->addBreadcrumb($strName, $strUrl);
        return $this;
    }

    public function save(): self
    {
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

    public function delete(): self
    {
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
