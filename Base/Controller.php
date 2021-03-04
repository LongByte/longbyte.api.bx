<?php

namespace Api\Core\Base;

use Bitrix\Main\Context;

/**
 * Class \Api\Core\Base\Controller
 */
class Controller {

    const RESPONSE_TYPE_JSON = 0;
    const RESPONSE_TYPE_RAW = 1;

    /**
     *
     * @var \Bitrix\Main\HttpRequest
     */
    protected $obRequest = null;

    /**
     *
     * @var int
     */
    protected $responseType = self::RESPONSE_TYPE_JSON;

    /**
     *
     * @var type 
     */
    protected $response = null;

    /**
     * 
     */
    public static function callController() {
        $obRequest = Context::getCurrent()->getRequest();

        $strModule = $obRequest->get('module');
        $strModule = strtoupper(substr($strModule, 0, 1)) . substr($strModule, 1);
        $strController = $obRequest->get('controller');
        $strController = strtoupper(substr($strController, 0, 1)) . substr($strController, 1);
        $strClassName = '\\Api\\Controller\\' . $strModule . '\\' . $strController;
        if (class_exists($strClassName)) {
            $obController = new $strClassName;
            $strMethod = strtolower($obRequest->getRequestMethod()) . 'Action';
            if (method_exists($obController, $strMethod)) {
                echo $obController->$strMethod();
            }
        }
    }

    /**
     * 
     */
    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
    }

    /**
     * 
     * @return \Bitrix\Main\HttpRequest
     */
    protected function getRequest(): \Bitrix\Main\HttpRequest {
        return $this->obRequest;
    }
    
    /**
     * 
     * @return string
     */
    protected function getPostData(): string {
        return $this->getRequest()->getInput();
    }

    /**
     * 
     * @return \Api\Core\Base\Controller\Response
     */
    protected function getResponse(): \Api\Core\Base\Controller\Response {
        if (is_null($this->response)) {
            $this->response = new \Api\Core\Base\Controller\Response();
        }
        return $this->response;
    }

    /**
     * 
     * @param int $responseType
     * @return $this
     */
    protected function setResponseType(int $responseType) {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * 
     */
    protected function get() {
        
    }

    /**
     * 
     * @return mixed
     */
    private function getAction() {
        $this->get();
        return $this->exitAction();
    }

    /**
     * 
     */
    protected function post() {
        
    }

    /**
     * 
     * @return mixed
     */
    private function postAction() {
        $this->post();
        return $this->exitAction();
    }

    protected function put() {
        
    }

    /**
     * 
     * @return mixed
     */
    private function putAction() {
        $this->put();
        return $this->exitAction();
    }

    /**
     * 
     */
    protected function delete() {
        
    }

    /**
     * 
     * @return mixed
     */
    private function deleteAction() {
        $this->delete();
        return $this->exitAction();
    }

    /**
     * 
     * @return string
     */
    protected function exitAction(): string {
        if (is_null($this->response)) {
            return '';
        }
        switch (strtolower($this->strResponseType)) {
            case self::RESPONSE_TYPE_RAW:
                return $this->response;
                break;
            case self::RESPONSE_TYPE_JSON:
            default:
                header('Content-Type: application/json');
                if ($this->response instanceof \Api\Core\Base\Controller\Response) {
                    return \Bitrix\Main\Web\Json::encode($this->response->toArray());
                } else {
                    return \Bitrix\Main\Web\Json::encode($this->response);
                }
                break;
        }
    }

}
