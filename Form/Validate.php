<?php

namespace Api\Core\Form;

use Api\Core\Form\Entity\Field;

abstract class Validate {

    const INTEGER = 1;
    const STRING = 2;
    const FILE = 4;
    const ALL = 8;

    protected $_constants = array(
        'text' => self::STRING,
        'counter' => self::STRING,
        'date' => self::STRING,
        'hidden' => self::STRING,
        'textarea' => self::STRING,
        'email' => self::STRING,
        'tel' => self::STRING,
        'password' => self::STRING,
        'radio' => self::INTEGER,
        'checkbox' => self::INTEGER,
        'checkbox-link' => self::INTEGER,
        'select' => self::INTEGER,
        'captcha' => self::STRING,
        'file' => self::FILE
    );
    protected $_value;
    protected $_messageTemplates = array();
    protected $_errors = array();

    /**
     * @var integer
     */
    protected $_type = 2;

    /**
     * @var Field
     */
    protected $_field;
    protected $_messages;

    public function __construct() {
        
    }

    abstract public function isValid($value);

    /**
     * @return int
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * @param null $type
     * @return $this
     * @throws \Exception
     */
    protected function setType($type = null) {

        if (is_string($type) && isset($this->_constants[$type])) {
            $iType = $this->_constants[$type];
        }

        if (!is_int($iType) || ($iType < 0) || ($iType > self::ALL)) {
            throw new \Exception('Unknown type ' . $type);
        }

        $this->_type = $iType;
        return $this;
    }

    /**
     * @param Field $obField
     * @return $this
     * @throws \Exception
     */
    public function setField(Field $obField) {
        $this->_field = $obField;
        $this->setType($obField->getType());
        return $this;
    }

    /**
     * @return Field
     */
    public function getField() {
        return $this->_field;
    }

    /**
     * 
     * @param type $value
     * @return $this
     */
    protected function _setValue($value) {
        $this->_value = $value;
        $this->_messages = array();
        $this->_errors = array();
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function isFieldSet() {
        if ($this->_field instanceof Field) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param type $messageKey
     * @param type $value
     * @param type $arOptions
     */
    protected function _error($messageKey, $value = null, $arOptions = array()) {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            $messageKey = current($keys);
        }
        if ($value === null) {
            $value = $this->_value;
        }
        $this->_errors[$messageKey] = $this->_createMessage($messageKey, $value, $arOptions);
    }

    /**
     * 
     * @param type $messageKey
     * @param type $value
     * @param type $arOptions
     * @return string
     */
    protected function _createMessage($messageKey, $value, $arOptions = array()) {
        //$messageKey
        $strFormMessage = $this->getField()->getParent()->getMessage($messageKey);
        if (is_null($strFormMessage)) {
            if (!isset($this->_messageTemplates[$messageKey])) {
                return null;
            }
            $message = $this->_messageTemplates[$messageKey];
            $value = strval($value);
            $strFormMessage = str_replace('%value%', $value, $message);
            foreach ($arOptions as $strOptionKey => $strOptionMessage) {
                $strFormMessage = str_replace('%' . $strOptionKey . '%', $strOptionMessage, $strFormMessage);
            }
        }

        return $strFormMessage;
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
     * @param type $messageKey
     * @param type $strMessage
     * @return $this
     */
    public function setMessage($messageKey, $strMessage) {
        $this->_messageTemplates[$messageKey] = $strMessage;
        return $this;
    }

}
