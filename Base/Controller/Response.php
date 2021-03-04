<?php

namespace Api\Core\Base\Controller;

/**
 * Class \Api\Core\Base\Controller\Response
 */
class Response {

    /**
     *
     * @var array
     */
    protected $data = array();

    /**
     *
     * @var array
     */
    protected $errors = array();

    /**
     *
     * @var bool
     */
    protected $success = true;

    /**
     * 
     * @param array $arData
     * @return $this
     */
    public function setData(array $arData) {
        $this->data = $arData;
        return $this;
    }

    /**
     * 
     * @param string $strError
     * @return $this
     */
    public function addError(string $strError) {
        $this->errors[] = $strError;
        $this->success = false;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function hasErrors(): bool {
        return count($this->errors);
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array {
        return array(
            'data' => $this->data,
            'errors' => $this->errors,
            'success' => $this->success,
        );
    }

}
