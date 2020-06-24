<?php

namespace Api\Core\Form\Entity\Field\Option;

/**
 * Class \Api\Core\Form\Entity\Field\Option\Entity
 */
class Entity {

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     *
     * @var type 
     */
    protected $_value = null;

    /**
     *
     * @var string
     */
    protected $_label = null;

    /**
     *
     * @var bool
     */
    protected $_selected = false;

    /**
     *
     * @var string
     */
    protected $_ref = null;

    /**
     *
     * @param string $name
     * @param string|array $value
     * @return $this
     * @throws \Exception
     */
    public function setAttribute($name, $value) {
        $name = strval($name);
        if ('_' == $name[0]) {
            throw new \Exception(sprintf('Invalid attribute "%s"; must not contain a leading underscore', $name));
        }

        if (null === $value) {
            if (isset($this->_attributes[$name])) {
                unset($this->_attributes[$name]);
            }
            if (isset($this->$name)) {
                unset($this->$name);
            }
        } else {
            $this->_attributes[$name] = $value;
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($key, $default = null) {
        $key = (string) $key;
        if (array_key_exists($key, $this->_attributes)) {
            return $this->_attributes[$key];
        }
        return $default;
    }

    /**
     * @return array
     */
    public function getAttributes() {
        return $this->_attributes;
    }

    /**
     * 
     * @param string $value
     * @return $this
     */
    public function setValue($value) {
        $this->_value = $value;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * 
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getLabel() {
        return $this->_label;
    }

    /**
     * 
     * @param bool $bSelected
     * @return $this
     */
    public function setSelected($bSelected = true) {
        $this->_selected = (bool) $bSelected;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function isSelected() {
        return $this->_selected;
    }

    /**
     * 
     * @param string $ref
     * @return $this
     */
    public function setRef($ref) {
        $this->_ref = $ref;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getRef() {
        return $this->_ref;
    }

    /**
     * 
     * @return array
     */
    public function toArray() {

        $arData = $this->getAttributes();
        $arData['value'] = $this->getValue();
        $arData['label'] = $this->getLabel();
        $arData['ref'] = $this->getRef();
        $arData['selected'] = $this->isSelected();

        return $arData;
    }

}
