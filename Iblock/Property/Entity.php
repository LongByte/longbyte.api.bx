<?php

namespace Api\Core\Iblock\Property;

/**
 * Class \Api\Core\Iblock\Property\Entity
 * 
 * @method int getId()
 * @method $this setId(int $iId)
 * @method bool hasId()
 * @method \Bitrix\Main\Type\DateTime getTimestampX()
 * @method $this setTimestampX(\Bitrix\Main\Type\DateTime $obTimestampX)
 * @method bool hasTimestampX()
 * @method int getIblockId()
 * @method $this setIblockId(int $iIblockId)
 * @method bool hasIblockId()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method bool hasName()
 * @method boolean getActive()
 * @method $this setActive(boolean $bActive)
 * @method bool hasActive()
 * @method int getSort()
 * @method $this setSort(int $iSort)
 * @method bool hasSort()
 * @method string getCode()
 * @method $this setCode(string $strCode)
 * @method bool hasCode()
 * @method text getDefaultValue()
 * @method $this setDefaultValue(text $mixedDefaultValue)
 * @method bool hasDefaultValue()
 * @method enum getPropertyType()
 * @method $this setPropertyType(enum $mixedPropertyType)
 * @method bool hasPropertyType()
 * @method int getRowCount()
 * @method $this setRowCount(int $iRowCount)
 * @method bool hasRowCount()
 * @method int getColCount()
 * @method $this setColCount(int $iColCount)
 * @method bool hasColCount()
 * @method enum getListType()
 * @method $this setListType(enum $mixedListType)
 * @method bool hasListType()
 * @method boolean getMultiple()
 * @method $this setMultiple(boolean $bMultiple)
 * @method bool hasMultiple()
 * @method string getXmlId()
 * @method $this setXmlId(string $strXmlId)
 * @method bool hasXmlId()
 * @method string getFileType()
 * @method $this setFileType(string $strFileType)
 * @method bool hasFileType()
 * @method int getMultipleCnt()
 * @method $this setMultipleCnt(int $iMultipleCnt)
 * @method bool hasMultipleCnt()
 * @method string getTmpId()
 * @method $this setTmpId(string $strTmpId)
 * @method bool hasTmpId()
 * @method int getLinkIblockId()
 * @method $this setLinkIblockId(int $iLinkIblockId)
 * @method bool hasLinkIblockId()
 * @method boolean getWithDescription()
 * @method $this setWithDescription(boolean $bWithDescription)
 * @method bool hasWithDescription()
 * @method boolean getSearchable()
 * @method $this setSearchable(boolean $bSearchable)
 * @method bool hasSearchable()
 * @method boolean getFiltrable()
 * @method $this setFiltrable(boolean $bFiltrable)
 * @method bool hasFiltrable()
 * @method boolean getIsRequired()
 * @method $this setIsRequired(boolean $bIsRequired)
 * @method bool hasIsRequired()
 * @method enum getVersion()
 * @method $this setVersion(enum $mixedVersion)
 * @method bool hasVersion()
 * @method string getUserType()
 * @method $this setUserType(string $strUserType)
 * @method bool hasUserType()
 * @method text getUserTypeSettingsList()
 * @method $this setUserTypeSettingsList(text $mixedUserTypeSettingsList)
 * @method bool hasUserTypeSettingsList()
 * @method text getUserTypeSettings()
 * @method $this setUserTypeSettings(text $mixedUserTypeSettings)
 * @method bool hasUserTypeSettings()
 * @method string getHint()
 * @method $this setHint(string $strHint)
 * @method bool hasHint()
 * 
 * @method mixed getValue()
 * @method $this setValue(mixed $mixedValue)
 * @method bool hasValue()
 * @method mixed getValueXmlId()
 * @method $this setValueXmlId(mixed $mixedValueXmlId)
 * @method bool hasValueXmlId()
 * @method mixed getValueId()
 * @method $this setValueId(mixed $mixedValueId)
 * @method bool hasValueId()
 * @method mixed getDescription()
 * @method $this setDescription(mixed $mixedDescription)
 * @method bool hasDescription()
 */
class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var \Api\Core\Iblock\Property\Value\Entity
     */
    protected $_obValueObject = null;

    /**
     *
     * @var \Api\Core\Iblock\Property\Value\Collection
     */
    protected $_obValuesCollection = null;

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return string
     */
    public static function getCollection(): string {
        return Collection::class;
    }

    /**
     * 
     * @return array
     */
    public static function getTableFields(): array {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     * 
     * @return array
     */
    public function getFields(): array {
        $arFields = static::getTableFields();
        $arFields[] = 'VALUE';
        $arFields[] = 'VALUE_XML_ID';
        $arFields[] = 'VALUE_ID';
        $arFields[] = 'DESCRIPTION';
        return $arFields;
    }

    /**
     * 
     * @return bool
     */
    public function isMultiple(): bool {
        return $this->getMultiple() == 'Y';
    }

    /**
     * 
     * @return bool
     */
    public function isWithDescription(): bool {
        return $this->getWithDescription() == 'Y';
    }

    /**
     * 
     * @return bool
     */
    public function isFileProperty(): bool {
        return $this->getPropertyType() == \Bitrix\Iblock\PropertyTable::TYPE_FILE;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Property\Value\Entity|null
     */
    public function getValueObject(): ?\Api\Core\Iblock\Property\Value\Entity {
        if (!$this->isMultiple()) {
            if (is_null($this->_obValueObject)) {
                $entityClass = '\Api\Core\Iblock\Property\Value\Entity';
                if ($this->isFileProperty()) {
                    $entityClass = '\Api\Core\Iblock\Property\Value\Entity\File';
                }
                $this->_obValueObject = new $entityClass(array(
                    'VALUE' => $this->getValue(),
                    'VALUE_XML_ID' => $this->getValueXmlId(),
                    'VALUE_ID' => $this->getValueId(),
                    'DESCRIPTION' => $this->getDescription(),
                ));
            }
        }
        return $this->_obValueObject;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Property\Value\Collection|null
     */
    public function getValuesCollection(): ?\Api\Core\Iblock\Property\Value\Collection {
        if ($this->isMultiple()) {
            if (is_null($this->_obValuesCollection)) {
                $entityClass = '\Api\Core\Iblock\Property\Value\Entity';
                if ($this->getPropertyType() == \Bitrix\Iblock\PropertyTable::TYPE_FILE) {
                    $entityClass = '\Api\Core\Iblock\Property\Value\Entity\File';
                }
                $obCollection = new \Api\Core\Iblock\Property\Value\Collection();
                foreach ($this->getValue() as $keyValue => $mixedValue) {
                    $obValue = new $entityClass(array(
                        'VALUE' => $mixedValue,
                        'VALUE_XML_ID' => $this->getValueXmlId()[$keyValue],
                        'VALUE_ID' => $this->getPropertyValueId()[$keyValue],
                        'DESCRIPTION' => $this->getDescription()[$keyValue],
                    ));

                    $obCollection->addItem($obValue);
                }
                $this->_obValuesCollection = $obCollection;
            }
        }
        return $this->_obValuesCollection;
    }

    /**
     * 
     * @return array|null
     */
    public function getData(): ?array {
        return null;
    }

    /**
     * 
     * @return null
     */
    public function save() {
        return null;
    }

    /**
     * 
     * @return null
     */
    public function delete() {
        return null;
    }

    /**
     * 
     * @return array|string
     */
    public function toSaveFormat() {
        $result = '';
        if ($this->isMultiple()) {
            $result = array();
            /** @var \Api\Core\Iblock\Property\Value\Entity $obValue */
            foreach ($this->getValuesCollection() as $obValue) {
                if ($this->isWithDescription()) {
                    if ($this->isFileProperty()) {
                        $arSaveValue = $obValue->getSaveValue();
                        if (is_null($arSaveValue)) {
                            continue;
                        }
                        $resultIndex = $obValue->getValueId() ?: $this->_getNewIndex($result);
                        $result[$resultIndex] = array(
                            'VALUE' => $obValue->getSaveValue(),
                            'DESCRIPTION' => $obValue->getDescription(),
                        );
                    } else {
                        $result[] = array(
                            'VALUE' => $obValue->getValue(),
                            'DESCRIPTION' => $obValue->getDescription(),
                        );
                    }
                } else {
                    $obValue = $this->getValueObject();
                    if ($this->isFileProperty()) {
                        $arSaveValue = $obValue->getSaveValue();
                        if (is_null($arSaveValue)) {
                            continue;
                        }
                        $resultIndex = $obValue->getValueId() ?: $this->_getNewIndex($result);
                        $result[$resultIndex] = $obValue->getSaveValue();
                    } else {
                        $result[] = $obValue->getValue();
                    }
                }
            }
        } else {
            $obValue = $this->getValueObject();
            if ($this->isWithDescription()) {
                $result = array(
                    'VALUE' => $obValue->getValue(),
                    'DESCRIPTION' => $obValue->getDescription(),
                );
            } else {
                $result = $obValue->getValue();
            }
        }
        return $result;
    }

    private function _getNewIndex(array $arResult): string {
        $iNewIndex = 0;
        foreach ($arResult as $key => $value) {
            if ($key == 'n' . $iNewIndex) {
                $iNewIndex++;
            }
        }
        return 'n' . $iNewIndex;
    }

}
