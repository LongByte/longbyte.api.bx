<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;

/**
 * Class \Api\Core\Form\Validate\GoogleCaptcha
 *
 */
class GoogleCaptcha extends Validate {

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid captcha",
    );

    /**
     * 
     * @param type $value
     * @return boolean
     */
    public function isValid($value) {
        $obReCaptcha = new \Api\Core\Form\Validate\GoogleCaptcha\Request($value);
        if (!$obReCaptcha->check()) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
