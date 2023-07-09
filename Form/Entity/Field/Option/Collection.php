<?php

namespace Api\Core\Form\Entity\Field\Option;

/**
 * Class \Api\Core\Form\Entity\Field\Option\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    /**
     *
     * @var array
     */
    protected $_keySelected = array();

    /**
     *
     * @param \Api\Core\Form\Entity\Field\Option\Entity $obOption
     * @return $this
     */
    public function addItem($obOption): self
    {
        $this->_collection[] = $obOption;
        $this->_keys[] = $obOption->getValue();
        if ($obOption->isSelected()) {
            $this->_keySelected[] = $obOption->getValue();
        }
        return $this;
    }

    /**
     *
     * @param mixed $value
     * @return $this
     */
    public function setOptionsValue($value)
    {
        $this->_keySelected = array();
        /** @var \Api\Core\Form\Entity\Field\Option\Entity $obOption */
        foreach ($this->getCollection() as $keyOption => $obOption) {
            if (is_array($value) && in_array($obOption->getValue(), $value) || !is_array($value) && $obOption->getValue() == $value) {
                $obOption->setSelected();
                $this->_keySelected[] = $keyOption;
            }
        }
        return $this;
    }

    /**
     *
     * @return \Api\Core\Form\Entity\Field\Option\Collection
     */
    public function getSelectedOptions()
    {
        $obSelectedOptions = new \Api\Core\Form\Entity\Field\Option\Collection();
        /** @var \Api\Core\Form\Entity\Field\Option\Entity $obOption */
        foreach ($this->_keySelected as $iKeySelected) {
            if ($obOption = $this->getByKey($iKeySelected)) {
                $obSelectedOptions->addItem($obOption);
            }
        }
        return $obSelectedOptions;
    }

}
