<?php

namespace Masterpass\CoreSDK\Exceptions;

class SDKValidationException extends SDKBaseException{

	const ERR_MSG_PRIVATE_KEY = "Private key can not be empty";
	const ERR_MSG_CONSUMER_ID = "Consumer Key can not be empty";

	public function __construct($errorMessage){
		parent::__construct($errorMessage);
	}
}
