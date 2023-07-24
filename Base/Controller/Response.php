<?php

namespace Api\Core\Base\Controller;

/**
 * Class \Api\Core\Base\Controller\Response
 */
class Response
{

    protected array $data = array();
    protected array $errors = array();
    protected bool $success = true;

    public function setData(array $arData): self
    {
        $this->data = $arData;
        return $this;
    }

    public function addError(string $strError): self
    {
        $this->errors[] = $strError;
        $this->success = false;
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors);
    }

    public function clearErrors(): self
    {
        $this->errors = array();
        $this->success = true;
        return $this;
    }

    public function toArray(): array
    {
        return array(
            'data' => $this->data,
            'errors' => $this->errors,
            'success' => $this->success,
        );
    }

}
