<?php


/**
 * Thrown to indicate all validation errors from the SDK.
 * @class SDKValidationException | Thrown when require parameters missing.
 * 
 * @package  MasterCardCoreSDK
 * @subpackage  Exception
 *
 */
class SDKValidationException extends SDKBaseException{
	
	const ERR_MSG_PRIVATE_KEY = "Private key can not be empty";
	const ERR_MSG_CONSUMER_ID = "Consumer Key can not be empty";
	/**
	 * @method Custom Exception SDKValidationException
	 * 
	 * @param errorMessage
	 */
	public function __construct($errorMessage){
		parent::__construct($errorMessage);
	}
}

?>