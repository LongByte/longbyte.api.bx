<?php

namespace Api\Core\Form\Validate\File;

/**
 * Class \Api\Core\Form\Validate\File\Extension
 *
 */
class Extension extends \Api\Core\Form\Validate\File\Exist {

    /**
     * @const string Error constants
     */
    const FALSE_EXTENSION = 'fileExtensionFalse';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::FALSE_EXTENSION => "File '%value%' has a false extension",
    );

    /**
     * Internal list of extensions
     * @var array
     */
    protected $_extension = null;

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'extension' => '_extension'
    );

    /**
     * Sets validator options
     *
     * @param array $arOptions
     * @return void
     */
    public function __construct(array $arOptions) {
        $this->setExtension($arOptions);
    }

    /**
     * Returns the set file extension
     *
     * @return array
     */
    public function getExtension() {
        return $this->_extension;
    }

    /**
     * Sets the file extensions
     *
     * @param array $arExtensions The extensions to validate
     * @return $this
     */
    public function setExtension(array $arExtensions) {
        $this->_extension = null;
        $this->addExtension($arExtensions);
        return $this;
    }

    /**
     * Adds the file extensions
     *
     * @param array $arExtensions The extensions to add for validation
     * @return $this
     */
    public function addExtension($arExtensions) {
        $arExistExtensions = $this->getExtension();


        foreach ($arExtensions as $strExtension) {
            if (empty($strExtension) || !is_string($strExtension)) {
                continue;
            }
            $arExistExtensions[] = trim($strExtension);
        }
        $arExistExtensions = array_unique($arExistExtensions);

        // Sanity check to ensure no empty values
        foreach ($arExistExtensions as $key => $ext) {
            if (empty($ext)) {
                unset($arExistExtensions[$key]);
            }
        }
        $this->_extension = $arExistExtensions;
        return $this;
    }

    /**
     * 
     * @param type $value
     * @return boolean
     */
    public function isValid($value) {
        // Is file readable ?
        if (parent::isValid($value)) {
            //тут используем другое имя
            $obFile = new \Realweb\Api\Model\FileSystem\File($value['name']);
            $strFileExtension = $obFile->getExtension();
            foreach ($this->getExtension() as $strExtension) {
                if (strtolower($strExtension) == strtolower($strFileExtension)) {
                    return true;
                }
            }
            $this->_error(self::FALSE_EXTENSION, $value['name']);
            return false;
        }
        return true;
    }

}
