<?php

namespace Api\Core\Form\Validate\File;

/**
 * Class \Api\Core\Form\Validate\File\Exist
 *
 */
class Exist extends \Api\Core\Form\Validate
{

    /**
     * @const string Error constants
     */
    const NOT_FOUND = 'fileExtensionNotFound';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::NOT_FOUND => "File '%value%' is not readable or does not exist",
    );

    public function __construct()
    {

    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function isValid($value)
    {
        // Is file readable ?
        $obFile = new \Realweb\Api\Model\FileSystem\File($value['tmp_name']);
        if (!$obFile->isExist()) {
            $this->_error(self::NOT_FOUND, $value['name']);
            return false;
        }
        return true;
    }

}
