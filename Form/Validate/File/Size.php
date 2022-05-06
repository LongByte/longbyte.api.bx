<?php

namespace Api\Core\Form\Validate\File;

/**
 * Class \Api\Core\Form\Validate\File\Size
 *
 */
class Size extends \Api\Core\Form\Validate\File\Exist
{

    /**
     * @const string Error constants
     */
    const TOO_BIG = 'fileSizeTooBig';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::TOO_BIG => "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected",
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'max' => '_max',
    );

    /**
     * Maximum filesize
     *
     * If null, there is no maximum filesize
     *
     * @var integer|null
     */
    protected $_max;

    /**
     * Sets validator options
     *
     * @param int $iMaxSize
     * @return void
     */
    public function __construct(int $iMaxSize)
    {
        $this->setMax($iMaxSize);
    }

    /**
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_max;
    }

    /**
     *
     * @param int $iMaxSize
     * @return $this
     */
    public function setMax(int $iMaxSize)
    {
        $this->_max = $iMaxSize;
        return $this;
    }

    public function isValid($value)
    {
        // Is file readable ?
        if (parent::isValid($value)) {
            $obFile = new \Realweb\Api\Model\FileSystem\File($value['tmp_name']);
            $iSize = $obFile->getSize();
            if ($iSize <= $this->getSize()) {
                return true;
            }
            $this->_error(self::TOO_BIG, $value['name'], array(
                'max' => \CFile::FormatSize($this->getSize()),
                'size' => \CFile::FormatSize($iSize),
            ));
            return false;
        }
        return true;
    }

}
