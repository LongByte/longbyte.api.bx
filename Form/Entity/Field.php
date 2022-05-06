<?php

namespace Api\Core\Form\Entity;

use Api\Core\Form\Entity;
use Api\Core\Form\Validate;
use Api\Core\Form\Validate\Notempty;

/**
 * Class Field
 * @package Api\Core\Form\Entity
 */
class Field
{

    /**
     *
     */
    const OPTION_FIELDS = array('select', 'radio', 'checkbox');

    /**
     * @var
     */
    protected $_label;

    /**
     * @var bool
     */
    protected $_required = false;

    /**
     * @var
     */
    protected $_name;

    /**
     * @var
     */
    protected $_type;

    /**
     * @var
     */
    protected $_value;

    /**
     * @var bool
     */
    protected $_checked = false;

    /**
     * @var
     */
    protected $_default;

    /**
     * @var
     */
    protected $_order;

    /**
     * @var \Api\Core\Form\Entity\Field\Option\Collection
     */
    protected $_options = null;

    /**
     * @var bool
     */
    protected $_isArray = false;

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @var bool
     */
    protected $_isError = false;

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     *
     * @var Entity
     */
    protected $_form;

    /**
     *
     * @var Validate[]
     */
    protected $_validators = array();

    /**
     * @var callable
     */
    private $_value_callback = null;

    /**
     * @var callable
     */
    private $_structure_value_callback = null;

    /**
     * Field constructor.
     * @param $strName
     * @throws \Exception
     */
    public function __construct($strName)
    {
        $this->setName($strName);
    }

    /**
     * @return bool
     */
    public function isGroup()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isOptionField()
    {
        if (in_array($this->getType(), self::OPTION_FIELDS)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isCheckbox()
    {
        if ($this->getType() == "checkbox") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isRadio()
    {
        if ($this->getType() == "radio") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getType() == "hidden") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isSelect()
    {
        if ($this->getType() == "select") {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isLogic()
    {
        if ($this->isCheckbox()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        if ($this->getType() == "file") {
            return true;
        }

        return false;
    }

    /**
     * @param $flag
     * @return $this
     */
    public function setIsArray($flag)
    {
        $this->_isArray = (bool) $flag;
        return $this;
    }

    /**
     * Is the element representing an array?
     *
     * @return bool
     */
    public function isArray()
    {
        return $this->_isArray;
    }

    /**
     * @param Entity $obForm
     * @return $this
     */
    public function setParent(Entity $obForm)
    {
        $this->_form = $obForm;
        return $this;
    }

    /**
     * @return Entity
     */
    public function getParent()
    {
        return $this->_form;
    }

    /**
     *
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function setName($name)
    {
        $name = $this->filterName($name);
        if ('' === $name) {
            throw new \Exception('Invalid name provided; must contain only valid variable characters and be non-empty');
        }

        $this->_name = $name;
        return $this;
    }

    /**
     * @param $value
     * @param bool $allowBrackets
     * @return string|string[]|null
     */
    public function filterName($value, $allowBrackets = false)
    {
        $charset = '^a-zA-Z0-9_\x7f-\xff-';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     *
     * @param string $name
     * @param string|array $value
     * @return $this
     * @throws \Exception
     */
    public function setAttribute($name, $value)
    {
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

        if ($name == 'checked') {
            $bChecked = ($this->_attributes[$name] ? true : false);
            $this->setChecked($bChecked);
        }

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($key, $default = null)
    {
        $key = (string) $key;
        if (array_key_exists($key, $this->_attributes)) {
            return $this->_attributes[$key];
        }
        return $default;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = strval($label);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     *
     * @param bool $flag
     * @return $this
     */
    public function setRequired($flag = true)
    {
        $this->_required = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->_required;
    }

    /**
     *
     * @param bool $flag
     * @return $this
     */
    public function setChecked($flag = true)
    {
        $this->_checked = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->_checked;
    }

    /**
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        if (is_callable($this->getValueCallback())) {
            $value = call_user_func_array($this->getValueCallback(), array($value));
        }
        $this->_value = $value;
        if ($this->isOptionField()) {
            $this->getOptions()->setOptionsValue($value);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function cleanValue()
    {
        $this->_value = null;
        return $this;
    }

    /**
     * @return array|string
     */
    public function getValue()
    {
        return is_array($this->_value) ? $this->_value : strval($this->_value);
    }

    /**
     *
     * @param mixed $value
     * @return $this
     */
    public function setDefault($value)
    {
        $this->_default = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return strval($this->_default);
    }

    /**
     *
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->_order = intval($order);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     *
     * @param mixed $value
     * @return $this
     */
    public function setType($value)
    {
        $this->_type = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {

        return $this->_type;
    }

    /**
     * @param \Api\Core\Form\Entity\Field\Option\Entity|string $obValue
     * @param string $label depticated
     * @param bool $isEmpty depticated
     * @return $this
     */
    public function addOption($obValue, $label = '', $isEmpty = false)
    {
        if (!($obValue instanceof \Api\Core\Form\Entity\Field\Option\Entity)) {
            $value = (string) $obValue;
            $obValue = new \Api\Core\Form\Entity\Field\Option\Entity();
            $obValue
                ->setValue($value)
                ->setLabel($label)
            ;
        }

        $this->getOptions()->addItem($obValue);
        return $this;
    }

    /**
     * @return \Api\Core\Form\Entity\Field\Option\Collection
     */
    public function getOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = new \Api\Core\Form\Entity\Field\Option\Collection();
        }

        return $this->_options;
    }

    /**
     *
     * @return \Api\Core\Form\Entity\Field\Option\Collection
     */
    public function getSelectedOptions()
    {
        return $this->getOptions()->getSelectedOptions();
    }

    /**
     *
     * @return boolean
     */
    public function hasOptions()
    {
        return $this->getOptions()->count() > 0;
    }

    /**
     * @param $value
     * @param null $context
     * @return bool
     * @throws \Exception
     */
    public function isValid($value = null, $context = null)
    {
        if ($value !== null) {
            $this->setValue($value);
        }
        $value = $this->getValue();

        if ((('' === $value) || (null === $value)) && !$this->isRequired() && !$this->getValidators()) {
            return true;
        }

        if ($this->isRequired() && !$this->getValidator('NotEmpty')) {
            $obNotEmpty = new Notempty();
            $this->addValidator($obNotEmpty);
        }

        $this->_errors = array();
        $result = true;
        $isArray = $this->isArray();

        foreach ($this->getValidators() as $key => $obValidator) {
            if ($isArray && is_array($value)) {
                $errors = array();
                /**
                 * ToDo
                 */
            } elseif ($obValidator->isValid($value, $context)) {
                continue;
            } else {
                $result = false;
                $errors = $obValidator->getErrors();
            }

            $result = false;
            foreach ($errors as $strKey => $error) {
                $i = 0;
                $strKey = $strKey . $i;
                while (array_key_exists($strKey, $this->_errors)) {
                    $i++;
                    $strKey = $strKey . $i;
                }
                $this->_errors[$strKey] = $error;
            }
        }

        return $result;
    }

    /**
     *
     * @param string $strName
     * @return Validate
     */
    public function getValidator($strName)
    {
        if (!isset($this->_validators[$strName])) {
            $strLen = strlen($strName);
            foreach ($this->_validators as $key => $obValidator) {
                if (is_string($obValidator)) {
                    $obValidator = $this->_loadValidator($obValidator);
                }
                $strLocalName = substr(strrchr(get_class($obValidator), "\\"), 1);

                if ($strLen > strlen($strLocalName)) {
                    continue;
                }
                if (0 === substr_compare($strLocalName, $strName, -$strLen, $strLen, true)) {
                    return $obValidator;
                }
            }
            return false;
        }

        if (is_array($this->_validators[$strName])) {
            return $this->_loadValidator($this->_validators[$strName]);
        }

        return $this->_validators[$strName];
    }

    /**
     * @param string $strValidator
     * @return bool
     */
    protected function _loadValidator(string $strValidator)
    {
        $strClassName = "\\Realweb\\Api\\Module\\Form\\Model\\Validate\\" . \Realweb\Helper::upFirst($strValidator);
        if (class_exists($strClassName)) {
            $obValidator = new $strClassName();
            $obValidator->setField($this);

            return $obValidator;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getValidators()
    {
        $arValidators = array();
        foreach ($this->_validators as $key => $value) {
            if ($value instanceof Validate) {
                if (!$value->isFieldSet()) {
                    $value->setField($this);
                }
                $arValidators[$key] = $value;
                continue;
            }
            $obValidator = $this->_loadValidator($value);
            $strClassName = substr(strrchr(get_class($obValidator), "\\"), 1);
            $arValidators[] = $obValidator;
        }
        return $arValidators;
    }

    /**
     * @param array $arValidators
     * @return Field
     * @throws \Exception
     */
    public function setValidators(array $arValidators)
    {
        $this->clearValidators();
        return $this->addValidators($arValidators);
    }

    /**
     *
     * @return $this
     */
    public function clearValidators()
    {
        $this->_validators = array();
        return $this;
    }

    /**
     *
     * @param array $arValidators
     * @return $this
     * @throws \Exception
     */
    public function addValidators(array $arValidators)
    {
        foreach ($arValidators as $validatorInfo) {
            if (is_string($validatorInfo)) {
                $this->addValidator($validatorInfo);
            } elseif ($validatorInfo instanceof Validate) {
                $this->addValidator($validatorInfo);
            } else {
                throw new \Exception('Invalid validator passed to addValidators()');
            }
        }

        return $this;
    }

    /**
     * @param $validator
     * @return $this
     * @throws \Exception
     */
    public function addValidator($validator)
    {
        if ($validator instanceof Validate) {
            $name = get_class($validator);
        } elseif (is_string($validator)) {
            $name = $validator;
        } else {
            throw new \Exception('Invalid validator provided to addValidator; must be string or Zend_Validate_Interface');
        }
        $this->_validators[] = $validator;

        return $this;
    }

    /**
     * @return callable
     */
    public function getValueCallback()
    {
        return $this->_value_callback;
    }

    /**
     * @param callable $value_callback
     * @return $this
     */
    public function setValueCallback(callable $value_callback)
    {
        $this->_value_callback = $value_callback;
        return $this;
    }

    /**
     * @return callable
     */
    public function getStructureValueCallback()
    {
        return $this->_structure_value_callback;
    }

    /**
     * @param callable $structure_value_callback
     * @return $this
     */
    public function setStructureValueCallback(callable $structure_value_callback)
    {
        $this->_structure_value_callback = $structure_value_callback;
        return $this;
    }

    /**
     * @return bool
     */
    protected function _hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     *
     * @param array $arErrors
     * @return $this
     */
    public function setErrors($arErrors)
    {
        $this->_errors = $arErrors;
        return $this;
    }

    /**
     *
     * @param string $strError
     * @return $this
     */
    public function addError($strError)
    {
        $this->_errors[] = $strError;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return md5(serialize($this));
    }

    /**
     * @return array|mixed|string
     */
    public function getStructureValue()
    {
        if (is_callable($this->getStructureValueCallback())) {
            return call_user_func_array($this->getStructureValueCallback(), array($this->getValue(), $this));
        } else {
            return $this->getValue();
        }
    }

    /**
     * @return array
     */
    public function getStructure()
    {
        $arField = array(
            'name' => $this->getName(),
            'order' => $this->getOrder(),
            'visible' => true,
            'type' => $this->getType(),
            'label' => $this->getLabel(),
            'value' => $this->getStructureValue(),
            'errors' => $this->getErrors(),
            'is_group' => $this->isGroup(),
            'isError' => false,
            'hash' => $this->getHash(),
        );


        if ($this->isGroup()) {
            $arField['fields'] = $this->_getFields();
        } else {
            $arField['multiple'] = $this->isArray();
            $arField['default'] = $this->getDefault();
            if (count($arField['errors']) > 0) {
                $arField ['isError'] = true;
            }

            if ($this->isOptionField() || $this->hasOptions()) {
                $this->getOptions()->setOptionsValue($this->getValue());
                /** @var \Api\Core\Form\Entity\Field\Option\Entity $obOption */
                foreach ($this->getOptions() as $obOption) {
                    $obOption->setRef($this->getName());
                }
                $arField['options'] = $this->getOptions()->toArray();
            }
            if ($this->isCheckbox()) {
                $arField['checked'] = $this->isChecked();
            }
        }

        foreach ($this->getAttributes() as $key => $value) {
            if (!isset($arField[$key])) {
                $arField[$key] = $value;
            }
        }
        return $arField;
    }

}
