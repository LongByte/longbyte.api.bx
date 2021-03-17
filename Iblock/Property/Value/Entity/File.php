<?php

namespace Api\Core\Iblock\Property\Value\Entity;

/**
 * Class \Api\Core\Iblock\Property\Value\Entity\File
 */
class File extends \Api\Core\Iblock\Property\Value\Entity {

    /**
     *
     * @var bool
     */
    protected $bMarkToDelele = false;

    /**
     *
     * @var \Api\Core\Main\File\Entity
     */
    protected $obFile = null;

    /**
     * 
     * @param bool $bMarkToDelele
     * @return $this
     */
    public function markDelete(bool $bMarkToDelele = true): self {
        $this->bMarkToDelele = $bMarkToDelele;
        return $this;
    }

    /**
     * 
     * @return \Api\Core\Main\File\Entity|null
     */
    public function getFile(): ?\Api\Core\Main\File\Entity {
        if (is_null($this->obFile) && is_numeric($this->getValue())) {
            $this->obFile = new \Api\Core\Main\File\Entity($this->getValue());
        }
        return $this->obFile;
    }

    /**
     * 
     * @param \Api\Core\Main\File\Entity $obFile
     * @return $this
     */
    public function setFile(\Api\Core\Main\File\Entity $obFile): self {
        $this->obFile = $obFile;
        return $this;
    }

    /**
     * 
     * @return mixed
     */
    public function getSaveValue() {
        if ($this->bMarkToDelele) {
            return array('del' => 'Y');
        } elseif (!$this->getValueId()) {
            return parent::getValue();
        } else {
            return null;
        }
    }

}
