<?php

namespace Api\Core\Form\Entity\Field;

use Realweb\Api\Model\Utils\Helper;

class File extends \Api\Core\Form\Entity\Field
{

    /**
     *
     * @param type $value
     * @return $this
     */
    public function setValue($value)
    {
        if ($this->isArray() && is_array($value) && is_string(current($value))) {
            $this->_value = array();
            foreach ($value as $valueItem) {
                $this->_value[] = $this->_getFileArray($valueItem);
            }
        } elseif (is_string($value)) {
            $this->_value = $this->_getFileArray($value);
        } elseif ($this->isArray()) {
            $this->_value = Helper::makeFileArray($value);
        } else {
            $this->_value = $value;
        }

        return $this;
    }

    /**
     *
     * @param type $value
     * @return type
     */
    private function _getFileArray($value)
    {
        $arFileName = explode('.', $value);
        $strDirName = $arFileName[0];
        $strDirPath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array(
                "upload",
                "tmp",
                $strDirName,
            ));
        $obPublicFile = new \Realweb\Api\Model\FileSystem\File\PublicFile($strDirPath . DIRECTORY_SEPARATOR . $value);

        return array(
            'name' => $obPublicFile->getOriginalName(),
            'type' => $obPublicFile->getMimeType(),
            'tmp_name' => $obPublicFile->getName(),
            'error' => 0,
            'size' => $obPublicFile->getSize(),
        );
    }

    /**
     * @return array|mixed|string
     * @throws \Exception
     */
    public function getStructureValue()
    {
        $value = $this->getValue();
        if (($this->isArray() && is_array($value) && is_string(current($value))) || is_string($value)) {
            return $value;
        } elseif ($this->isArray() && is_array($value)) {
            $arValues = array();
            $arFiles = array();
            foreach ($value as $valueItem) {
                if (!empty($valueItem)) {
                    $obFile = new \Realweb\Api\Model\FileSystem\File($valueItem['tmp_name']);
                    $arValues[] = $obFile->getOriginalName();
                    $arFiles[] = $this->_getUploadedFile($obFile);
                }
            }
            $value = $arValues;
            $this->setAttribute('uploaded_file', $arFiles);
        } elseif (is_array($value) && !empty($value['tmp_name'])) {
            $obFile = new \Realweb\Api\Model\FileSystem\File($value['tmp_name']);
            $value = $obFile->getOriginalName();
            $this->setAttribute('uploaded_file', array($this->_getUploadedFile($obFile)));
        }

        return $value;
    }

    /**
     * @param \Realweb\Api\Model\FileSystem\File $obFile
     * @return array
     */
    private function _getUploadedFile(\Realweb\Api\Model\FileSystem\File $obFile)
    {
        return array(
            'name' => $obFile->getOriginalName(),
            'size' => $obFile->getSize(),
            'public_path' => $obFile->getPublicPath(),
            'hash' => $obFile->getHash()
        );
    }

}
