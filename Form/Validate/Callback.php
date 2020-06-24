<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;

/**
 * Class \Api\Core\Form\Validate\Callback
 * 
 */
class Callback extends Validate {

    const INVALID_CALLBACK = 'callbackInvalid';
    const INVALID_VALUE = 'callbackValue';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_VALUE => "'%value%' is not valid",
        self::INVALID_CALLBACK => "An exception has been raised within the callback",
    );
    protected $_callback = null;
    protected $_options = array();

    /**
     * 
     * @param type $callback
     * @throws \Exception
     */
    public function __construct($callback = null) {
        parent::__construct();

        if (is_callable($callback)) {
            $this->setCallback($callback);
        } elseif (is_array($callback)) {
            if (isset($callback['callback'])) {
                $this->setCallback($callback['callback']);
            }
            if (isset($callback['options'])) {
                $this->setOptions($callback['options']);
            }
        }

        if (null === ($initializedCallack = $this->getCallback())) {
            throw new \Exception('No callback registered');
        }
    }

    /**
     * 
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * 
     * @param array $options
     * @return $this
     */
    public function setOptions($options) {
        $this->_options = (array) $options;
        return $this;
    }

    /**
     * 
     * @return mixed
     */
    public function getCallback() {
        return $this->_callback;
    }

    /**
     * 
     * @param type $callback
     * @return $this
     * @throws \Exception
     */
    public function setCallback($callback) {
        if (!is_callable($callback)) {
            throw new \Exception('Invalid callback given');
        }
        $this->_callback = $callback;
        return $this;
    }

    /**
     * 
     * @param type $value
     * @return boolean
     */
    public function isValid($value) {
        $this->_setValue($value);

        $options = $this->getOptions();
        $callback = $this->getCallback();
        $args = func_get_args();


        $options = array_merge($args, $options);

        try {
            if (!call_user_func_array($callback, $options)) {
                $this->_error(self::INVALID_VALUE);
                return false;
            }
        } catch (\Exception $e) {
            $this->_error(self::INVALID_CALLBACK);
            return false;
        }

        return true;
    }

}
