<?php

namespace Masterpass\CoreSDK\Exceptions;

use RuntimeException;

class SDKBaseException extends RuntimeException
{
	public function __construct($errorMessage)
	{
		parent::__construct($errorMessage);
	}

}

?>