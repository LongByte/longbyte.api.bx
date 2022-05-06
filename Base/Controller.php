<?php

namespace Api\Core\Base;

use Bitrix\Main\Context;

/**
 * Class \Api\Core\Base\Controller
 */
class Controller
{

    const RESPONSE_TYPE_JSON = 0;
    const RESPONSE_TYPE_RAW = 1;

    protected ?\Bitrix\Main\HttpRequest $obRequest = null;
    protected int $responseType = self::RESPONSE_TYPE_JSON;
    /** @var mixed|null */
    protected $response = null;
    protected ?string $rawPost = null;

    public static function callController(): void
    {
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
        \CMain::FinalActions();
    }

    public function __construct()
    {
        $this->obRequest = Context::getCurrent()->getRequest();
    }

    protected function getRequest(): \Bitrix\Main\HttpRequest
    {
        return $this->obRequest;
    }

    protected function getPostData(): string
    {
        if (is_null($this->rawPost)) {
            $this->rawPost = $this->getRequest()->getInput();
        }
        return $this->rawPost;
    }

    protected function getResponse(): \Api\Core\Base\Controller\Response
    {
        if (is_null($this->response)) {
            $this->response = new \Api\Core\Base\Controller\Response();
        }
        return $this->response;
    }

    public function setPostData($rawPost): self
    {
        $this->rawPost = $rawPost;
        return $this;
    }

    protected function setResponseType(int $responseType): self
    {
        $this->responseType = $responseType;
        return $this;
    }

    protected function get()
    {

    }

    private function getAction()
    {
        $this->get();
        return $this->exitAction();
    }

    protected function post()
    {

    }

    private function postAction()
    {
        $this->post();
        return $this->exitAction();
    }

    protected function put()
    {

    }

    private function putAction()
    {
        $this->put();
        return $this->exitAction();
    }

    protected function delete()
    {

    }

    private function deleteAction()
    {
        $this->delete();
        return $this->exitAction();
    }

    protected function exitAction(): string
    {
        if (is_null($this->response)) {
            return '';
        }
        switch ($this->responseType) {
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
        }
    }

}
