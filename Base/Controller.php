<?php

namespace Api\Core\Base;

use Bitrix\Main\Context;

/**
 * Class \Api\Core\Base\Controller
 */
class Controller {

    /**
     *
     * @var \Bitrix\Main\HttpRequest
     */
    protected $obRequest = null;

    /**
     *
     * @var string
     */
    protected $rawPost = null;

    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->rawPost = file_get_contents('php://input');
    }

    /**
     * 
     * @return \Bitrix\Main\HttpRequest
     */
    protected function getRequest() {
        return $this->obRequest;
    }

    /**
     * 
     * @return string
     */
    protected function getPostData() {
        return $this->rawPost;
    }

    /**
     * 
     * @param mixed $rawPost
     * @return $this
     */
    public function setPostData($rawPost) {
        $this->rawPost = $rawPost;
        return $this;
    }

}
