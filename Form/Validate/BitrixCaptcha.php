<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;
use Bitrix\Main\Context;

/**
 * Class \Api\Core\Form\Validate\BitrixCaptcha
 *
 */
class BitrixCaptcha extends Validate
{

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => 'Invalid captcha',
    );

    /**
     *
     * @param string $captcha_word
     * @return boolean
     */
    public function isValid($captcha_word)
    {

        /** @var \CMain $APPLICATION */
        global $APPLICATION;

        if (!$APPLICATION->CaptchaCheckCode($captcha_word, Context::getCurrent()->getRequest()->get('captcha_sid'))) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
