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
     * @param array $data
     */
    public function __construct(array $data = array()) {
        parent::__construct($data);
        if (is_numeric($data['VALUE'])) {
            $this->getFile();
        }
    }

    /**
     * 
     * @param mixed $value
     * @return $this
     */
    public function setValue($value) {
        parent::setValue($value);
        if (is_numeric($value)) {
            $this->getFile();
        }
        return $this;
    }

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
     * @return array|int
     */
    public function getSaveValue() {
        if ($this->bMarkToDelele) {
            /* Удаление файла */
            return array('del' => 'Y');
        } elseif (!$this->getValueId()) {
            /* Новый файл */
            return parent::getValue();
        } else {
            /* Оставить текущий, как есть */
            return (int) parent::getValue();
        }
    }

}
