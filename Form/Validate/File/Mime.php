<?php

namespace Api\Core\Form\Validate\File;

/**
 * Class \Api\Core\Form\Validate\File\Mime
 *
 */
class Mime extends \Api\Core\Form\Validate\File\Exist
{

    /**
     * @const string Error constants
     */
    const FALSE_TYPE = 'fileMimeTypeFalse';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::FALSE_TYPE => "File '%value%' has a false mimetype of '%type%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'type' => '_type',
    );

    /**
     * Mimetypes
     *
     * If null, there is no mimetype
     *
     * @var array|null
     */
    protected $_mimetype;

    /**
     * Sets validator options
     *
     * @param array $arOptions
     * @return void
     */
    public function __construct(array $arOptions)
    {
        $this->setMimeType($arOptions);
    }

    public function getMimeType()
    {
        return $this->_mimetype;
    }

    /**
     *
     * @param array $arTypes
     * @return $this
     */
    public function setMimeType(array $arTypes)
    {
        $this->_mimetype = null;
        $this->addMimeType($arTypes);
        return $this;
    }

    /**
     *
     * @param array $arTypes
     * @return $this
     */
    public function addMimeType($arTypes)
    {
        $arExist = $this->getMimeType();
        if (!is_array($arExist)) {
            $arExist = array();
        }
        foreach ($arTypes as $strType) {
            if (empty($strType) || !is_string($strType)) {
                continue;
            }
            $arExist[] = trim($strType);
        }
        $arExist = array_unique($arExist);

        // Sanity check to ensure no empty values
        foreach ($arExist as $key => $value) {
            if (empty($value)) {
                unset($arExist[$value]);
            }
        }
        $this->_mimetype = $arExist;
        return $this;
    }

    /**
     *
     * @param type $value
     * @return boolean
     */
    public function isValid($value)
    {
        // Is file readable ?
        if (parent::isValid($value)) {
            $obFile = new \Realweb\Api\Model\FileSystem\File($value['tmp_name']);
            $strMimeType = $obFile->getMimeType();

            foreach ($this->getMimeType() as $strType) {
                if (strpos($strMimeType, $strType) !== false) {
                    return true;
                }
            }
            $this->_error(self::FALSE_TYPE, $value['name'], array('type' => $strMimeType));
            return false;
        }
        return true;
    }

}
