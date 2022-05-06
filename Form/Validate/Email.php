<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;

/**
 * Class \Api\Core\Form\Validate\Email
 *
 */
class Email extends Validate
{

    const INVALID = 'emailAddressInvalid';
    const INVALID_FORMAT = 'emailAddressInvalidFormat';
    const DOT_ATOM = 'emailAddressDotAtom';
    const QUOTED_STRING = 'emailAddressQuotedString';
    const INVALID_LOCAL_PART = 'emailAddressInvalidLocalPart';
    const LENGTH_EXCEEDED = 'emailAddressLengthExceeded';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::INVALID_FORMAT => "'%value%' is no valid email address in the basic format local-part@hostname",
        self::DOT_ATOM => "'%localPart%' can not be matched against dot-atom format",
        self::QUOTED_STRING => "'%localPart%' can not be matched against quoted-string format",
        self::INVALID_LOCAL_PART => "'%localPart%' is no valid local part for email address '%value%'",
        self::LENGTH_EXCEEDED => "'%value%' exceeds the allowed length",
    );

    /**
     * @var string
     */
    protected $_hostname;

    /**
     * @var string
     */
    protected $_localPart;

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        if (strlen($value) == 0) {
            return true;
        }

        $matches = array();
        $length = true;
        $this->_setValue($value);

        // Split email address up and disallow '..'
        if ((strpos($value, '..') !== false) or (!preg_match('/^(.+)@([^@]+)$/', $value, $matches))) {
            $this->_error(self::INVALID_FORMAT);
            return false;
        }

        $this->_localPart = $matches[1];
        $this->_hostname = $matches[2];

        if ((strlen($this->_localPart) > 64) || (strlen($this->_hostname) > 255)) {
            $length = false;
            $this->_error(self::LENGTH_EXCEEDED);
        }

        $local = $this->_validateLocalPart();

        if ($local && $length) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    private function _validateLocalPart()
    {
        // First try to match the local part on the common dot-atom format
        $result = false;

        // Dot-atom characters are: 1*atext *("." 1*atext)
        // atext: ALPHA / DIGIT / and "!", "#", "$", "%", "&", "'", "*",
        //        "+", "-", "/", "=", "?", "^", "_", "`", "{", "|", "}", "~"
        $atext = 'a-zA-Z0-9\x21\x23\x24\x25\x26\x27\x2a\x2b\x2d\x2f\x3d\x3f\x5e\x5f\x60\x7b\x7c\x7d\x7e';
        if (preg_match('/^[' . $atext . ']+(\x2e+[' . $atext . ']+)*$/', $this->_localPart)) {
            $result = true;
        } else {
            // Try quoted string format
            // Quoted-string characters are: DQUOTE *([FWS] qtext/quoted-pair) [FWS] DQUOTE
            // qtext: Non white space controls, and the rest of the US-ASCII characters not
            //   including "\" or the quote character
            $noWsCtl = '\x01-\x08\x0b\x0c\x0e-\x1f\x7f';
            $qtext = $noWsCtl . '\x21\x23-\x5b\x5d-\x7e';
            $ws = '\x20\x09';
            if (preg_match('/^\x22([' . $ws . $qtext . '])*[$ws]?\x22$/', $this->_localPart)) {
                $result = true;
            } else {
                $this->_error(self::DOT_ATOM);
                $this->_error(self::QUOTED_STRING);
                $this->_error(self::INVALID_LOCAL_PART);
            }
        }

        return $result;
    }

}
