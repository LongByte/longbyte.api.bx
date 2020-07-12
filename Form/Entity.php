<?php

namespace Api\Core\Form;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Mail\Event;
use Api\Core\Utils\ArrayHelper;
use Api\Core\Form\Entity\Field;
use Api\Core\Form\Entity\Group;

if (class_exists('\Bitrix\Main\Localization\Loc')) {
    \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
}

/**
 * Class Entity
 * @package Api\Core\Form
 */
class Entity {

    const METHOD_DELETE = 'delete';
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';

    /**
     * @var
     */
    public $method;

    /**
     * @var
     */
    public $title;

    /**
     * @var
     */
    public $name;

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var array
     */
    protected $_methods = array('delete', 'get', 'post', 'put');

    /**
     * @var bool
     */
    protected $_errorsExist = false;

    /**
     * @var bool
     */
    protected $_isSuccess = false;

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @var string
     */
    protected $_type = 'form';

    /**
     * @var
     */
    protected $_text;

    /**
     * @var Field
     */
    protected $submit;

    /**
     *
     * @var Field|Group
     */
    protected $price;

    /**
     *
     * @var Field[]|Group[]
     */
    protected $_elements = array();

    /**
     * @var string
     */
    protected $_mail_event;

    /**
     * @var string
     */
    protected $_success_message;

    /**
     *
     * @var \Bitrix\Main\HttpRequest
     */
    private $request;

    /**
     * @var array
     */
    private $_captcha;

    /**
     * @var bool
     */
    private $_record_event;

    /**
     * Entity constructor.
     */
    public function __construct() {
        $this->init();
    }

    /**
     * @throws \Exception
     */
    public function init() {
        $this->setMethod($this->getFormMethod());
        $this->setName($this->getFormName());
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (substr($method, 0, 5) === '_init') {
                $this->$method();
            }
        }
        $this->initForm();
    }

    /**
     *
     */
    public function initForm() {
        
    }

    /**
     * @return $this
     */
    public function clean() {
        foreach ($this->getElements() as $obElement) {
            $obElement->cleanValue();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     *
     * @param string $strType
     * @return $this
     */
    public function setType($strType) {
        $this->_type = $strType;
        return $this;
    }

    /**
     * @return array
     */
    public function getStructure() {
        $arForm = array(
            'name' => $this->getName(),
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'captcha' => $this->getCaptcha(),
            'id' => $this->getName(),
            'action' => $this->getAction(),
            'method' => $this->getMethod(),
            'fields' => $this->_getFields(),
            'submit' => $this->_getSubmitField(),
            'success' => $this->isSuccess(),
            'success_message' => $this->getSuccessMessage(),
            'errors' => $this->getErrors(),
            'text' => $this->getText(),
            'hash' => $this->getHash(),
        );

        foreach ($this->getAttributes() as $key => $value) {
            if (!isset($arForm[$key])) {
                $arForm[$key] = $value;
            }
        }

        return $arForm;
    }

    /**
     * @return string
     */
    public function getHash() {
        return md5(serialize($this));
    }

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->_isSuccess;
    }

    /**
     * @param bool $bSuccess
     * @return $this
     */
    public function setSuccess($bSuccess = true) {
        $this->_isSuccess = $bSuccess;
        return $this;
    }

    /**
     * @return array
     */
    protected function _getFields() {
        $arFields = array();
        foreach ($this->_elements as $obElement) {
            $strType = $obElement->getType();
            if ($strType == 'submit') {
                $this->_setSubmit($obElement);
                continue;
            }
            $arFields[] = $this->_getField($obElement);
        }

        usort($arFields, function ($a, $b) {
            if (intval($a['order']) == intval($b['order'])) {
                return 0;
            }
            return (intval($a['order']) > intval($b['order']) ? 1 : -1);
        });

        return $arFields;
    }

    /**
     *
     * @param Field|Group $obElement
     * @return array
     */
    public function _getField($obElement) {
        $arField = $obElement->getStructure();
        return $arField;
    }

    /**
     *
     * @param Field $obElement
     * @return $this
     */
    protected function _setSubmit(Field $obElement) {
        $this->submit = $obElement;
        return $this;
    }

    /**
     *
     * @return Field
     */
    protected function _getSubmit() {
        if (is_null($this->submit)) {
            $this->_getFields();
        }
        return $this->submit;
    }

    /**
     *
     * @return null|array
     */
    protected function _getSubmitField() {
        $obSubmit = $this->_getSubmit();
        $arSubmit = null;
        if ($obSubmit instanceof Field) {
            $arSubmit = $this->_getField($obSubmit);
        }
        return $arSubmit;
    }

    /**
     * @param $action
     * @return Entity
     */
    public function setAction($action) {
        return $this->setAttribute('action', strval($action));
    }

    /**
     * @return mixed|string|null
     */
    public function getAction() {
        $action = $this->getAttribute('action');
        if (null === $action) {
            $action = '';
            $this->setAction($action);
        }
        return $action;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return strval($this->title);
    }

    /**
     *
     * @param string $strTitle
     * @return $this
     */
    public function setTitle($strTitle) {
        $this->title = $strTitle;

        return $this;
    }

    /**
     *
     * @param string $method
     * @return $this
     * @throws \Exception
     */
    public function setMethod($method) {
        $method = strtolower($method);
        if (!in_array($method, $this->_methods)) {
            throw new \Exception(sprintf('"%s" is an invalid form method', $method));
        }
        $this->setAttribute('method', $method);

        return $this;
    }

    /**
     * @return string
     */
    public function getMethod() {
        if (null === ($method = $this->getAttribute('method'))) {
            $method = self::METHOD_POST;
            $this->setAttribute('method', $method);
        }

        return strtolower($method);
    }

    /**
     * @return mixed
     */
    public function getFormMethod() {
        return $this->method;
    }

    /**
     *
     * @param string $name
     * @return $this
     * @throws \Exception
     */
    public function setName($name) {
        $name = $this->filterName($name);
        if ('' === (string) $name) {
            throw new \Exception('Invalid name provided; must contain only valid variable characters and be non-empty');
        }

        return $this->setAttribute('name', $name);
    }

    /**
     * @return mixed|null
     */
    public function getName() {
        return $this->getAttribute('name');
    }

    /**
     * @return string
     */
    public function getFormName() {
        return (strlen($this->name) > 0 ? $this->name : md5($this->title));
    }

    /**
     * @param $value
     * @param bool $allowBrackets
     * @return string|string[]|null
     */
    public function filterName($value, $allowBrackets = false) {
        $charset = '^a-zA-Z0-9_\x7f-\xff';
        if ($allowBrackets) {
            $charset .= '\[\]';
        }
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($key, $value) {
        $key = strval($key);
        $this->_attributes[$key] = $value;
        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($key, $default = null) {
        $key = (string) $key;
        if (isset($this->_attributes[$key])) {
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
     * @param $strName
     * @return Field|Group
     * @throws \Exception
     */
    public function createField($strName) {
        if (isset($this->_elements[$strName])) {
            return $this->_elements[$strName];
        }

        $obField = new Field($strName);
        $obField->setParent($this);
        return $obField;
    }

    /**
     *
     * @param string $strName
     * @return $this
     */
    public function deleteField($strName) {
        if (isset($this->_elements[$strName])) {
            unset($this->_elements[$strName]);
        }
        return $this;
    }

    /**
     * @param $strName
     * @return Field|Group
     */
    public function createGroup($strName) {
        if (isset($this->_elements[$strName])) {
            return $this->_elements[$strName];
        }

        $obGroup = new Group($strName);
        $obGroup->setParent($this);

        return $obGroup;
    }

    /**
     * @param $obField
     * @return $this
     */
    public function addField($obField) {
        $name = $obField->getName();
        $this->_elements[$name] = $obField;

        return $this;
    }

    /**
     * @param $name
     *
     * @return \Api\Core\Form\Entity\Field|\Api\Core\Form\Entity\Group
     */
    public function getElement($name) {
        $path = explode(".", $name);
        $name = array_shift($path);
        if (array_key_exists($name, $this->_elements)) {
            $obField = $this->_elements[$name];
            if (count($path) > 0) {
                /** @var Group $obField */
                return $obField->getElement(implode(".", $path));
            } else {
                /** @var Field $obField */
                return $obField;
            }
        }

        return null;
    }

    /**
     * @param array $arData
     * @return bool
     * @throws \Exception
     */
    public function isValid(array $arData) {
        $this->setValues($arData);
        $valid = true;
        foreach ($this->_elements as $key => $obElement) {
            $arCheck = $arData;
            if ($obElement->isGroup()) {
                /** @var Group $obElement */
                $valid = $obElement->isValid($arData);
                if ($errors = $obElement->getErrors()) {
                    $this->setError($obElement->getLabel() . ': ' . implode(', ', $errors));
                }
            } else {
                /** @var Field $obElement */
                $valid = $obElement->isValid(ArrayHelper::getValue($arCheck, $key, null), $arData) && $valid;
                if ($errors = $obElement->getErrors()) {
                    $this->setError($obElement->getLabel() . ': ' . implode(', ', $errors));
                }
            }
        }
        if ($this->isCaptcha() && !$this->checkCaptcha()) {
            $this->setError(Loc::getMessage('captchaFail'));
            $valid = false;
        }
        $this->_errorsExist = !$valid;

        return $valid && !$this->hasErrors();
    }

    /**
     * @param $strKey
     * @return string
     */
    public function getMessage($strKey) {
        if (class_exists('\Bitrix\Main\Localization\Loc')) {
            return \Bitrix\Main\Localization\Loc::getMessage($strKey);
        }
        return $strKey;
    }

    /**
     * @param $arData
     */
    public function setValues($arData) {
        foreach ($this->_elements as $name => &$obElement) {
            if ($obElement->isGroup()) {
                $obElement->setValues($arData);
            } else {
                if ($obElement->isCheckbox()) {
                    if (array_key_exists($name, $arData)) {
                        $obElement->setChecked(true);
                    } else {
                        $obElement->setChecked(false);
                    }
                } else {
                    if (array_key_exists($name, $arData)) {
                        $this->_setValueElement($obElement, $arData[$name]);
                    }
                }
            }
        }
    }

    /**
     * @param Field $obElement
     * @param $value
     */
    private function _setValueElement(&$obElement, $value) {
        $obElement->setValue($value);
        if ($obElement->isCheckbox()) {
            if (!empty($obElement->getValue())) {
                $obElement->setChecked();
            }
        }
    }

    /**
     * @return array
     */
    public function getValues() {
        $arValues = array();
        foreach ($this->_elements as $name => $obElement) {
            if ($obElement->isGroup()) {
                /** @var Group $obElement */
                $arValues[$name] = $obElement->getValues();
            } else {
                /** @var Field $obElement */
                $strValue = $obElement->getValue();
                if (strlen($strValue) == 0 && !is_array($strValue)) {
                    $strValue = $obElement->getDefault();
                }

                if (!empty($strValue)) {
                    if ($obElement->isArray()) {
                        if (is_array($strValue)) {
                            $arValue = $strValue;
                        } else {
                            $arValue = explode('-', $strValue);
                        }
                        $arValues[$name] = $arValue;
                    } elseif ($obElement->isCheckbox()) {
                        if ($obElement->isChecked()) {
                            $arValues[$name] = $strValue;
                        }
                    } else {
                        $arValues[$name] = $strValue;
                    }
                }
            }
        }

        return $arValues;
    }

    /**
     * @return bool
     */
    protected function _hasErrors() {
        return !empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     *
     * @param mixed $error
     * @return $this
     */
    public function setError($error) {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * @return Field[]|Group[]
     */
    public function getElements() {
        $arElements = $this->_elements;

        return $arElements;
    }

    /**
     *
     * @return \Bitrix\Main\HttpRequest
     */
    protected function _getRequest() {
        if (is_null($this->request)) {
            $obContext = \Bitrix\Main\Application::getInstance()->getContext();
            $this->request = $obContext->getRequest();
        }
        return $this->request;
    }

    /**
     * Only for GET forms, see \Api\Core\Form\Filter
     * @return $this
     */
    public function setElementValues() {
        foreach ($this->getElements() as $obElement) {
            $strFieldName = $obElement->getName();
            $strValue = $this->_getRequest()->get($strFieldName, null);
            if (!$obElement->isGroup()) {
                if ($obElement->isLogic()) {
                    //нам тут важно лишь наличие ключа, что отправлено не важно для этого типа поля
                    if (!is_null($strValue)) {
                        $obElement->setChecked();
                    }
                } else {
                    if (strlen($strValue) > 0 || ($obElement->isArray() && !empty($strValue))) {
                        $obElement->setValue($strValue);
                    } else {
                        $obElement->setValue($obElement->getDefault());
                    }
                }
            } else {
                $obElement->setElementValues();
            }
        }
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getElementValues() {
        $arValues = array();
        foreach ($this->getElements() as $obElement) {
            if ($obElement instanceof \Api\Core\Form\Entity\Field) {
                $arValues[$obElement->getName()] = $obElement->getValue();
            } elseif ($obElement instanceof \Api\Core\Form\Entity\Group) {
                $arValues = array_merge($arValues, $obElement->getElementValues());
            }
        }

        return $arValues;
    }

    /**
     *
     * @return \Api\Core\Form\Entity\Field[]
     */
    public function getNotEmptyElements() {
        $arElements = array();
        foreach ($this->getElements() as $obElement) {
            if ($obElement instanceof \Api\Core\Form\Entity\Field) {
                if ($obElement->isLogic()) {
                    if ($obElement->isChecked()) {
                        $arElements[] = $obElement;
                    }
                } else {
                    $strValue = $obElement->getValue();
                    if ((is_array($strValue) && !empty($strValue)) || strlen($strValue) > 0) {
                        $arElements[] = $obElement;
                    }
                }
            } elseif ($obElement instanceof \Api\Core\Form\Entity\Group) {
                $arElements = array_merge($arElements, $obElement->getNotEmptyElements());
            }
        }

        return $arElements;
    }

    /**
     *
     * @return array
     */
    public function getNotEmptyElementsValues() {
        $arElements = $this->getNotEmptyElements();
        $arValues = array();
        foreach ($arElements as $obElement) {
            $arValues[$obElement->getName()] = $obElement->getValue();
        }
        return $arValues;
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->_text;
    }

    /**
     *
     * @param string $strText
     * @return $this
     */
    public function setText($strText) {
        $this->_text = $strText;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors() {
        return $this->getErrors() ? true : false;
    }

    /**
     * @param array $arAdditionalFields
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public function sendMail(array $arAdditionalFields = array()) {
        if ($this->getMailEvent()) {
            $arEventFields = ArrayHelper::merge($this->getElementValues(), $arAdditionalFields);
            if ($this->isRecordEvent()) {
                $arFiles = array();
                foreach ($this->getElements() as $obElement) {
                    if ($obElement->isFile()) {
                        $arFiles[] = $obElement->getValue();
                    }
                }
                Event::send(array(
                    "EVENT_NAME" => $this->getMailEvent(),
                    "LID" => SITE_ID,
                    "C_FIELDS" => $arEventFields,
                    "FILE" => $arFiles
                ));
            } else {
                Event::sendImmediate(array(
                    "EVENT_NAME" => $this->getMailEvent(),
                    "LID" => SITE_ID,
                    "C_FIELDS" => $arEventFields,
                ));
            }
        }
    }

    /**
     * @param bool $is
     * @return $this
     */
    public function setRecordEvent(bool $is = true) {
        $this->_record_event = $is;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRecordEvent() {
        return $this->_record_event;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setSuccessMessage(string $message) {
        $this->_success_message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessMessage() {
        return $this->_success_message;
    }

    /**
     * @param string $strEvent
     * @return $this
     */
    public function setMailEvent(string $strEvent) {
        $this->_mail_event = $strEvent;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailEvent() {
        return $this->_mail_event;
    }

    /**
     * @param array $arParams
     * @return $this
     */
    public function setCaptcha(array $arParams = array()) {
        global $APPLICATION;

        if (!empty($arParams)) {
            $this->_captcha = $arParams;
        } else {
            $this->_captcha = array(
                'code' => $APPLICATION->CaptchaGetCode(),
                'google' => false
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCaptcha() {
        return $this->_captcha;
    }

    /**
     * @return bool
     */
    public function isCaptcha() {
        return !is_null($this->_captcha) ? true : false;
    }

    /**
     * @return bool
     */
    public function checkCaptcha() {
        global $APPLICATION;
        $obRequest = $this->_getRequest();

        if (!empty($obRequest->get('captcha_word')) && !empty($obRequest->get('captcha_sid'))) {
            return $APPLICATION->CaptchaCheckCode($obRequest->get('captcha_word'), $obRequest->get('captcha_sid'));
        }

        return false;
    }

}
