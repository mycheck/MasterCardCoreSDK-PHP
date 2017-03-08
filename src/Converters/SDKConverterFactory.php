<?php

namespace Masterpass\CoreSDK\Converters;

use Masterpass\CoreSDK\Exceptions\SDKConversionException;

/**
* To return specific converter object depending on request and response content type.
* @category Class | SDKConverterFactory
* @package  MasterCardCoreSDK
* @subpackage  Converters
*/

class SDKConverterFactory
{
	private $xml;
	private $urlEncoded;
	private $json;

	/** @const XML | Check to return XML converter object **/
	const XML = "XML";

	/** @const JSON | Check to return JSON converter object **/
	const JSON = "JSON";

	/** @const URLENCODED | Check to return URLENCODED converter object **/
	const URLENCODED = "X-WWW-FORM-URLENCODED";

	public function __construct()
	{

	}

	public static function getConverter($format)
	{

		switch($format)
		{
			case self::XML:
				return $xml = new XMLConverter();
			case self::URLENCODED:
				return $urlEncoded = new EncodedURLConverter();
			case self::JSON:
				return $json;
			default:
				throw new SDKConversionException("Converter not found for the format ".$format,"Converter not found for $format format");
		}
	}
}?>