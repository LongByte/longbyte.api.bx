<?php

namespace Api\Core\Iblock\Element;

/**
 * Class \Api\Core\Iblock\Element\Entity
 * 
 */
abstract class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var \Api\Core\Main\File\Entity
     */
    protected $_obPreviewPicture = null;

    /**
     *
     * @var \Api\Core\Main\File\Entity 
     */
    protected $_obDetailPicture = null;

    /**
     *
     * @var \Api\Core\Iblock\Property\Collection
     */
    protected $_obPropertyCollection = null;

    /**
     *
     * @var \Api\Core\Base\Collection
     */
    protected $_obTagCollection = null;

    /**
     *
     * @var array
     */
    protected $_arIProperty = null;

    /**
     * @var array
     */
    protected static $arProps = array();

    /**
     * 
     * @return \Api\Core\Main\File\Entity
     */
    public function getPreviewPictureFile(): ?\Api\Core\Main\File\Entity {
        $iFile = 0;
        if (is_null($this->_obPreviewPicture)) {
            if ($this->hasPreviewPicture()) {
                $iFile = $this->getPreviewPicture();
            }
            $this->_obPreviewPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obPreviewPicture;
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

    public function getTags() {
        if (is_null($this->_obTagCollection)) {
            $this->_obTagCollection = new \Api\Core\Base\Collection();
            $strTags = parent::getTags();
            $arTags = explode(',', $strTags);
            foreach ($arTags as $strTag) {
                $strTag = trim($strTag);
                if (strlen($strTag) <= 0) {
                    continue;
                }
                $obEntity = new \Api\Core\Iblock\Element\Tag\Entity($strTag, array('ID' => $strTag));
                $this->_obTagCollection->addItem($obEntity);
            }
        }
        return $this->_obTagCollection;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Property\Collection
     */
    public function getPropertyCollection(): \Api\Core\Iblock\Property\Collection {
        if (is_null($this->_obPropertyCollection)) {
            $this->_obPropertyCollection = new \Api\Core\Iblock\Property\Collection();
        }
        return $this->_obPropertyCollection;
    }

    /**
     * 
     * @return array
     */
    public function getProps(): array {
        return static::$arProps;
    }

    /**
     * 
     * @return array
     */
    public function getMeta(): ?array {
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
            'page_title' => $this->_arIProperty['ELEMENT_PAGE_TITLE'],
            'meta_title' => $this->_arIProperty['ELEMENT_META_TITLE'],
            'meta_keywords' => $this->_arIProperty['ELEMENT_META_KEYWORDS'],
            'meta_description' => $this->_arIProperty['ELEMENT_META_DESCRIPTION'],
        ));
        return $this;
    }

    /**
     * 
     * @return \self
     */
    public function addToBreadcrumbs(): self {
        $this->getMeta();

        $strName = $this->_arIProperty['ELEMENT_PAGE_TITLE'] ?: $this->getName();
        $strUrl = $this->hasDetailPageUrl() ? $this->getDetailPageUrl() : '';
        \Api\Core\Main\Seo::getInstance()->addBreadcrumb($strName, $strUrl);
        return $this;
    }

    /**
     * 
     * @return null|array
     */
    public function getData(): ?array {
        if (is_null($this->_data)) {
            $this->_data = array_fill_keys($this->getAllFields(), '');
            if (!is_null($this->_primary)) {
                $_arData = static::getModel()::getOneAsArray(array('ID' => $this->_primary));
                if ($_arData) {
                    $this->_data = $_arData;
                    $this->_exists = true;
                }
            }
        }
        return $this->_data;
    }

    /**
     * 
     * @return array
     */
    public function getAllFields(): array {
        $arFields = array();
        if (is_array($this->getFields())) {
            $arFields = array_merge($arFields, $this->getFields());
        }
        if (is_array($this->getProps())) {
            $arFields = array_merge($arFields, $this->getProps());
        }
        return $arFields;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments) {
        if ((strpos($name, "get") === 0)) {

            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return $arData[$strField];
            } elseif ($obProperty = $this->getPropertyCollection()->getByKey($strField)) {
                /** @var \Api\Core\Iblock\Property\Entity $obProperty */
                if ($arguments[0] == true) {
                    if ($obProperty->getMultiple() == 'Y') {
                        return $obProperty->getValuesCollection();
                    } else {
                        return $obProperty->getValueObject();
                    }
                } else {
                    return $obProperty->getValue();
                }
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } elseif ((strpos($name, "has") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                return true;
            } elseif ($this->getPropertyCollection()->getByKey($strField)) {
                return true;
            } else {
                return false;
            }
        } elseif ((strpos($name, "set") === 0)) {
            $strKey = substr_replace($name, "", 0, 3);
            preg_match_all('/[A-Z][^A-Z]*?/Us', $strKey, $res, PREG_SET_ORDER);
            $arField = array();
            foreach ($res as $arRes) {
                $arField[] = $arRes[0];
            }
            $strField = self::toUpper(implode('_', $arField));
            $arData = $this->_data;
            if (array_key_exists($strField, $arData)) {
                if ($this->checkChanges($this->_data[$strField], $arguments[0])) {
                    $this->_changed = true;
                }
                $this->_data[$strField] = $arguments[0];
                return $this;
            } elseif ($this->getPropertyCollection()->getByKey($strField)) {
                $this->getPropertyCollection()->getByKey($strField)->setValue($arguments[0]);
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } else {
            throw new \Exception("Call to undefined method {$name}");
        }
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

        $arProperties = array();

        foreach ($this->getProps() as $strProperty) {
            $arProperties[$strProperty] = $this->_data[$strProperty];
        }

        unset($arData['ID']);
        $arData['IBLOCK_ID'] = static::getModel()::getIblockId();
        $iId = $this->getId();

        $el = new \CIBlockElement;

        if (intval($iId) > 0) {
            $el->Update($iId, $arData);
            $this->_data = null;
            $this->getData();
        } else {
            $iId = $el->Add($arData);
            $this->setId($iId);
            $this->_primary = $iId;
        }

        if ($this->getId() > 0) {
            \CIBlockElement::SetPropertyValuesEx($this->getId(), static::getModel()::getIblockId(), $arProperties);
            $this->_exists = true;
            $this->_changed = false;
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
            \CIBlockElement::Delete($iId);
            $this->setId(0);
            $this->_primary = null;
            $this->_exists = false;
            $this->_changed = true;
        }
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function counterInc(): self {
        $iId = $this->getId();
        if (intval($iId) > 0) {
            \CIBlockElement::CounterInc($iId);
        }

        return $this;
    }

}
