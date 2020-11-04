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
class Entity extends \Api\Core\Base\Table\Entity {

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
    public function getFields(): array {
        $arFields = parent::getFields();
        $arFields[] = 'VALUE';
        $arFields[] = 'VALUE_XML_ID';
        $arFields[] = 'VALUE_ID';
        $arFields[] = 'DESCRIPTION';
        return $arFields;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Property\Value\Entity|null
     */
    public function getValueObject(): ?\Api\Core\Iblock\Property\Value\Entity {
        if ($this->getMultiple() == 'N') {
            return new \Api\Core\Iblock\Property\Value\Entity(array(
                'VALUE' => $this->getValue(),
                'VALUE_XML_ID' => $this->getValueXmlId(),
                'VALUE_ID' => $this->getValueId(),
                'DESCRIPTION' => $this->getDescription(),
            ));
        }
        return null;
    }

    /**
     * 
     * @return \Api\Core\Iblock\Property\Value\Collection|null
     */
    public function getValuesCollection(): ?\Api\Core\Iblock\Property\Value\Collection {
        if ($this->getMultiple() == 'Y') {

            $obCollection = new \Api\Core\Iblock\Property\Value\Collection();
            foreach ($this->getValue() as $keyValue => $mixedValue) {
                $obValue = new \Api\Core\Iblock\Property\Value\Entity(array(
                    'VALUE' => $mixedValue,
                    'VALUE_XML_ID' => $this->getValueXmlId()[$keyValue],
                    'VALUE_ID' => $this->getValueId()[$keyValue],
                    'DESCRIPTION' => $this->getDescription()[$keyValue],
                ));

                $obCollection->addItem($obValue);
            }

            return $obCollection;
        }
        return null;
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

}
