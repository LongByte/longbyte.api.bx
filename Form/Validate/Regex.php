<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;

class Regex extends Validate
{

    const INVALID = 'regexInvalid';
    const NOT_MATCH = 'regexNotMatch';
    const ERROROUS = 'regexErrorous';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Invalid type given. String, integer or float expected",
        self::NOT_MATCH => "'%value%' does not match against pattern '%pattern%'",
        self::ERROROUS => "There was an internal error while using the pattern '%pattern%'",
    );
    protected $_pattern;

    /**
     *
     * @param type $pattern
     */
    public function __construct($pattern)
    {
        parent::__construct();

        $this->setPattern($pattern);
    }

    /**
     * Returns the pattern option
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    /**
     * @param $pattern
     * @return $this
     * @throws \Exception
     */
    public function setPattern($pattern)
    {
        $this->_pattern = (string) $pattern;
        $status = @preg_match($this->_pattern, "Test");

        if (false === $status) {
            throw new \Exception("Internal error while using the pattern '$this->_pattern'");
        }

        return $this;
    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        $status = @preg_match($this->_pattern, $value);

        if (false === $status) {
            $this->_error(self::ERROROUS);
            return false;
        }

        if (!$status) {
            $this->_error(self::NOT_MATCH);
            return false;
        }
        return true;
    }

}
