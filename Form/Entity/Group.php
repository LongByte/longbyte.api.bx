<?php

namespace Api\Core\Form\Entity;

use Api\Core\Form\Entity;

class Group extends Entity {

    /**
     * @var Entity
     */
    protected $_form;
    protected $_errors = array();
    protected $_isError = false;
    protected $_name;
    protected $_label;
    protected $_order;

    /**
     * 
     * @param type $strName
     */
    public function __construct($strName) {
        $this->setName($strName);
    }

    public function isGroup() {
        return true;
    }

    /**
     * 
     * @return string
     */
    public function getType() {
        return 'group';
    }

    /**
     * @param Entity $obForm
     * @return $this
     */
    public function setParent(Entity $obForm) {
        $this->_form = $obForm;
        return $this;
    }

    /**
     * @return Entity
     */
    public function getParent() {
        return $this->_form;
    }

    /**
     *
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function setName($name) {
        $name = $this->filterName($name);
        if ('' === $name) {
            throw new \Exception('Invalid name provided; must contain only valid variable characters and be non-empty');
        }
        $this->_name = $name;

        return $this;
    }

    public function filterName($value, $allowBrackets = false) {
        $charset = '^a-zA-Z0-9_\x7f-\xff';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this|Entity
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

    public function getAttributes() {
        return $this->_attributes;
    }

    /**
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        $this->_label = strval($label);
        return $this;
    }

    public function getLabel() {
        return $this->_label;
    }

    /**
     *
     * @param int $order
     * @return $this
     */
    public function setOrder($order) {
        $this->_order = intval($order);
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function getOrder() {
        return $this->_order;
    }

    /**
     * 
     * @return null
     */
    public function getValue() {
        return null;
    }

    /**
     * 
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     * 
     * @return array
     */
    public function getStructure() {
        $arData = parent::getStructure();
        $arData['is_group'] = $this->isGroup();
        $arData['order'] = $this->getOrder();

        return $arData;
    }

}
