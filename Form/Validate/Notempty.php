<?php

namespace Api\Core\Form\Validate;

use Api\Core\Form\Validate;

class Notempty extends Validate
{

    const INVALID = 'invalid';
    const IS_EMPTY = 'isEmpty';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::IS_EMPTY => "Value is required and can't be empty",
        self::INVALID => "Invalid type given. String, integer, float, boolean or array expected",
    );

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function isValid($value)
    {

        if ($value !== null && !is_string($value) && !is_int($value) && !is_float($value) &&
            !is_bool($value) && !is_array($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $type = $this->getType();
        $value = !is_array($value) ? trim($value) : $value;

        $this->_setValue($value);

        // FILE
        if ($type == self::FILE) {
            if (!is_array($value) || count($value) == 0 || empty($value['name']) || (is_array($value['name']) && empty($value['name'][0]))) {
                $this->_error(self::IS_EMPTY);
                return false;
            }
        } else {

            // STRING ('')
            if ($type >= self::STRING) {
                $type -= self::STRING;
                if (strlen($value) == 0) {
                    $this->_error(self::IS_EMPTY);
                    return false;
                }
            }

            // INTEGER (0)
            if ($type >= self::INTEGER) {
                $type -= self::INTEGER;
                if (intval($value) == 0) {
                    $this->_error(self::IS_EMPTY);
                    return false;
                }
            }
        }

        return true;
    }

}
