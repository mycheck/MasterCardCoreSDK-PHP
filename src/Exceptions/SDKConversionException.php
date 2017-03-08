<?php

namespace Masterpass\CoreSDK\Exceptions;

class SDKConversionException extends SDKBaseException
{

    private $converterName;

    public function __construct($errorMessage, $converterName)
    {
        parent::__construct($errorMessage);
        $this->converterName = $converterName;
    }

    public function getConverterName()
    {
        return $this->converterName;
    }


}
