<?php

namespace Api\Core\Form\Validate;

class Phone extends Regex
{

    public function __construct()
    {
        //8(911)111-11-11
        $pattern = '/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/';
        parent::__construct($pattern);
    }

}
