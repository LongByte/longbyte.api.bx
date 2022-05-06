<?php

namespace Api\Core\Iblock\Element;

/**
 * Class \Api\Core\Iblock\Element\Entity
 */
abstract class Entity extends \Api\Core\Base\Entity
{

    protected ?\Api\Core\Main\File\Entity $_obPreviewPicture = null;
    protected ?\Api\Core\Main\File\Entity $_obDetailPicture = null;
    protected ?\Api\Core\Iblock\Property\Collection $_obPropertyCollection = null;
    protected ?array $_arIProperty = null;
    protected static array $arProps = array();
    protected ?\CIBlockElement $_CIBlockElement = null;

    public function getPreviewPictureFile(): ?\Api\Core\Main\File\Entity
    {
        $iFile = 0;
        if (is_null($this->_obPreviewPicture)) {
            if ($this->hasPreviewPicture()) {
                $iFile = (int) $this->getPreviewPicture();
            }
            $this->_obPreviewPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obPreviewPicture;
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

    public function getPropertyCollection(): \Api\Core\Iblock\Property\Collection
    {
        if (is_null($this->_obPropertyCollection)) {
            $this->_obPropertyCollection = new \Api\Core\Iblock\Property\Collection();
        }
        return $this->_obPropertyCollection;
    }

    private function setPropertyCollection(\Api\Core\Iblock\Property\Collection $obPropertyCollection): self
    {
        $this->_obPropertyCollection = $obPropertyCollection;
        return $this;
    }

    public function getProps(): array
    {
        return static::$arProps;
    }

    public function getMeta(): ?array
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
            'page_title' => $this->_arIProperty['ELEMENT_PAGE_TITLE'],
            'meta_title' => $this->_arIProperty['ELEMENT_META_TITLE'],
            'meta_keywords' => $this->_arIProperty['ELEMENT_META_KEYWORDS'],
            'meta_description' => $this->_arIProperty['ELEMENT_META_DESCRIPTION'],
        ));
        return $this;
    }

    public function addToBreadcrumbs(): self
    {
        $this->getMeta();

        $strName = $this->_arIProperty['ELEMENT_PAGE_TITLE'] ?: $this->getName();
        $strUrl = $this->hasDetailPageUrl() ? $this->getDetailPageUrl() : '';
        \Api\Core\Main\Seo::getInstance()->addBreadcrumb($strName, $strUrl);
        return $this;
    }

    public function getData(): ?array
    {
        if (is_null($this->_data)) {
            $this->_data = array_fill_keys($this->getFields(), '');
            if (!is_null($this->_primary)) {
                $_arData = static::getModel()::getOneAsArray(array('ID' => $this->_primary));
                if ($_arData) {
                    if (is_array($_arData['PROPERTIES'])) {
                        foreach ($_arData['PROPERTIES'] as $arProperty) {
                            if (!in_array($arProperty['CODE'], $this->getProps())) {
                                continue;
                            }
                            $obProperty = new \Api\Core\Iblock\Property\Entity($arProperty['ID'], $arProperty);
                            $this->getPropertyCollection()->addItem($obProperty);
                        }
                    }
                    unset($_arData['PROPERTIES']);

                    $this->_data = $_arData;
                    $this->_exists = true;
                }
            } else {
                /** @var \Api\Core\Iblock\Property\Collection $obCollection */
                $obCollection = \Api\Core\Iblock\Property\Model::getAll(array(
                    '=CODE' => $this->getProps(),
                    'IBLOCK_ID' => $this->getModel()::getIblockId()
                ),
                    0,
                    0,
                    array(
                        'select' => \Api\Core\Iblock\Property\Entity::getTableFields()
                    )
                );
                $this->setPropertyCollection($obCollection);
            }
        }
        return $this->_data;
    }

    public function getAllFields(): array
    {
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
    public function __call($name, $arguments)
    {
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
                    if ($obProperty->isMultiple()) {
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
                if ($this->checkChanges($this->getPropertyCollection()->getByKey($strField)->getValue(), $arguments[0])) {
                    $this->_changed = true;
                }
                $this->getPropertyCollection()->getByKey($strField)->setValue($arguments[0]);
                return $this;
            } else {
                throw new \Exception("Call to undefined method {$name}");
            }
        } else {
            throw new \Exception("Call to undefined method {$name}");
        }
    }

    public function save(): self
    {

        $arData = array();
        foreach ($this->getFields() as $strField) {
            $arData[$strField] = $this->_data[$strField];
        }

        $arProperties = array();

        foreach ($this->getProps() as $strProperty) {
            $obProperty = $this->getPropertyCollection()->getByKey($strProperty);
            if (!is_null($obProperty)) {
                $arProperties[$strProperty] = $obProperty->toSaveFormat();
            }
        }

        unset($arData['ID']);
        $arData['IBLOCK_ID'] = static::getModel()::getIblockId();
        $iId = $this->getId();

        $this->getCIBlockElement();

        if (intval($iId) > 0) {
            $this->getCIBlockElement()->Update($iId, $arData);
            $this->_data = null;
            $this->getData();
        } else {
            $iId = $this->getCIBlockElement()->Add($arData);
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

    public function delete(): self
    {
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

    public function counterInc(): self
    {
        $iId = $this->getId();
        if (intval($iId) > 0) {
            \CIBlockElement::CounterInc($iId);
        }

        return $this;
    }

    public function getCIBlockElement(): \CIBlockElement
    {
        if (is_null($this->_CIBlockElement)) {
            $this->_CIBlockElement = new \CIBlockElement();
        }
        return $this->_CIBlockElement;
    }

}
