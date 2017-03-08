<?php

namespace Masterpass\CoreSDK\Interceptors;

/**
* Interceptor to add authorization headers.
* @package  MasterCardCoreSDK
* @subpackage  Interceptor
* @category Class MasterCardSignatureInterceptor
**/

class MasterCardSignatureInterceptor {

	const AMP =  "&";
	const QUESTION = "?";
	const EMPTY_STRING = "";
	const EQUALS = "=";
	const DOUBLE_QUOTE = '"';
	const COMMA = ',';
	const ENCODED_TILDE = '%7E';
	const TILDE = '~';
	const COLON = ':';
	const SPACE = ' ';

	const UTF_8 = 'UTF-8';
	const V1 = 'v1';
	const OAUTH_START_STRING = 'OAuth ';
	const REALM = 'realm';
	const ACCEPT = 'Accept';
	const CONTENT_TYPE = 'Content-Type';
	const AUTHORIZATION = 'Authorization';
	const SSL_CA_CER_PATH_LOCATION = '/SSLCerts/EnTrust/cacert.pem';
	const PKEY = 'pkey';
	const SHA1 = "SHA1";
	const OAUTH_BODY_HASH = "oauth_body_hash";

	// Signature Base String
	const OAUTH_SIGNATURE = "oauth_signature";
	const OAUTH_CONSUMER_KEY = 'oauth_consumer_key';
	const OAUTH_NONCE = 'oauth_nonce';
	const SIGNATURE_METHOD = 'oauth_signature_method';
	const OAUTH_TIMESTAMP = 'oauth_timestamp';
	const OAUTH_CALLBACK = "oauth_callback";
	const OAUTH_SIGNATURE_METHOD = 'oauth_signature_method';
	const OAUTH_VERSION = 'oauth_version';

	const version = '1.0';
	const signatureMethod = 'RSA-SHA1';
	const realmValue = "eWallet";
	const AUTH_HEADER_INFO = "Authorization header added in the request.";

	public $signatureBaseString;
	public $authHeader;
	public static $configApiVal;
	private $privateKey;


	public function __construct()
    {
		$this->logger = Logger::getLogger(basename(__FILE__));
    }

	 /**
	 * function to send oauth headers to attach it to http requestMethod
	 * @param url
	 * @param method
	 * @param result
	 * @param serviceRequest
	 * @return headers
	 */

	public static function getReqHeaders($url,$method,$result,$serviceRequest,$config)
	{
			$reqHeaders = $serviceRequest->getHeaders();
			$reqContentType = $serviceRequest->getContentType();
			$body = $serviceRequest->getRequestBody();
			if(!empty($body))
			{
				$params[MasterCardSignatureInterceptor::OAUTH_BODY_HASH] = MasterCardSignatureInterceptor::generateBodyHash($result);
			} else
			{
				$params = '';
			}

			if(!empty($reqHeaders))
			{
				foreach($reqHeaders as $key=>$value)
				{
					$params[$key] = $value;
				}
			}


			$headers = array(
							MasterCardSignatureInterceptor::CONTENT_TYPE => $reqContentType,
							MasterCardSignatureInterceptor::AUTHORIZATION => self::buildAuthHeaderString($params,$url,$method,$result,$config)
							);

			return $headers;

	}

//---------- functions to generate OAuth headers and signature -----------------

	/** Method to generate the body hash
	 * @method Method to generate the body hash
	 * @param $body
	 * @return string
	 */
	protected static function generateBodyHash($body)
	{
		$sha1Hash = sha1($body, true);
		return base64_encode($sha1Hash);
	}

	/** This method generates and returns a unique nonce value to be used in Wallet API OAuth calls.
	 * @method This method generates and returns a unique nonce value to be used in Wallet API OAuth calls.
	 * @param $length
	 * @return string
	 */
	private static function generateNonce($length)
	{
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}
		else
		{
			$u = md5(uniqid('nonce_', true));
			return substr($u,0,$length);
		}
	}

	/** Builds a Auth Header used in connection to MasterPass services
	 * @method Builds a Auth Header used in connection to MasterPass services
	 * @param $params
	 * @param $realm
	 * @param $url
	 * @param $requestMethod
	 * @param $body
	 * @return string - Auth header
	 */
	 private static function buildAuthHeaderString($params,$url,$requestMethod,$body,$config)
	 {

		if(!empty($params))
		{
			$params = array_merge(self::OAuthParametersFactory($config),$params);
		} else $params = self::OAuthParametersFactory($config);

		$privateKey = $config->privateKey;

		try
		{
			$signature = self::generateAndSignSignature($params,$url,$requestMethod,$privateKey,$body);
		}
		catch(Exception $e)
		{
			throw new SDKOauthException($e);
		}

		$params[MasterCardSignatureInterceptor::OAUTH_SIGNATURE] = $signature;

		$params[MasterCardSignatureInterceptor::REALM] = MasterCardSignatureInterceptor::realmValue;

		$startString = MasterCardSignatureInterceptor::OAUTH_START_STRING;

		foreach ($params as $key => $value)
		{
			$startString = $startString.$key.MasterCardSignatureInterceptor::EQUALS.MasterCardSignatureInterceptor::DOUBLE_QUOTE.self::RFC3986urlencode($value).MasterCardSignatureInterceptor::DOUBLE_QUOTE.MasterCardSignatureInterceptor::COMMA;
		}

		$authHeader = substr($startString,0,strlen($startString)-1);
		return $authHeader;
	}

	/** Method to generate base string and generate the signature
	 * @method Method to generate base string and generate the signature
	 * @param $params
	 * @param $url
	 * @param $requestMethod
	 * @param $privateKey
	 * @param $body
	 * @return signature string
	 */
	private static function generateAndSignSignature($params,$url,$requestMethod,$privateKey,$body)
	{
		$baseString = self::generateBaseString($params,$url,$requestMethod);
		$signature = self::sign($baseString,$privateKey);
		return $signature;
	}

	/** Method to sign string
	 * @method Method to sign string
	 * @param $string
	 * @param $privateKey
	 * @return string
	 */
	private static function sign($string,$privateKey)
	{
		$privatekeyid = openssl_get_privatekey($privateKey);
		openssl_sign($string, $signature, $privatekeyid, OPENSSL_ALGO_SHA1);
		return base64_encode($signature);
	}

	/** Method to generate the signature base string
	 * @method Method to generate the signature base string
	 * @param $params
	 * @param $url
	 * @param $requestMethod
	 * @return string
	 */
	private static function generateBaseString($params,$url,$requestMethod)
	{
		$urlMap = parse_url($url);

		$url = self::formatUrl($url,$params);

		$params = self::parseUrlParameters($urlMap,$params);

		$baseString = strtoupper($requestMethod).MasterCardSignatureInterceptor::AMP.self::RFC3986urlencode($url).MasterCardSignatureInterceptor::AMP;
		ksort($params);

		$parameters = MasterCardSignatureInterceptor::EMPTY_STRING;
		foreach ($params as $key => $value)
		{
			$parameters = $parameters.$key.MasterCardSignatureInterceptor::EQUALS.self::RFC3986urlencode($value).MasterCardSignatureInterceptor::AMP;
		}
		$parameters = self::RFC3986urlencode(substr($parameters,0,strlen($parameters)-1));
		return $baseString.$parameters;
	}

	/** Method to extract the URL parameters and add them to the params array
	 * @method Method to extract the URL parameters and add them to the params array
	 * @param string $urlMap
	 * @param string $params
	 * @return string|multitype:
	 */
    static function parseUrlParameters($urlMap,$params)
	{
        if(empty($urlMap['query']))
		{
            return $params;
        }
        else
		{
            $str = $urlMap['query'];
            parse_str($str , $urlParamsArray );
            foreach ($urlParamsArray as $key => $value)
            {
                $urlParamsArray[$key] = self::RFC3986urlencode($value);
            }
            return array_merge($params,$urlParamsArray);
        }
    }

    /** Method to format the URL that is included in the signature base string
	 * @method Method to format the URL that is included in the signature base string
	 * @param string $url
	 * @param string $params
	 * @return string url string
	 */
	static function formatUrl($url,$params)
	{
		if(!parse_url($url))
		{
			return $url;
		}
		$urlMap = parse_url($url);
		return $urlMap['scheme'].'://'.$urlMap['host'].$urlMap['path'];
	}


	/** URLEncoder that conforms to the RFC3986 spec.
	 * @method URLEncoder that conforms to the RFC3986 spec.
	 * PHP's internal function, rawurlencode, does not conform to RFC3986 for PHP 5.2
	 * @param unknown $string
	 * @return unknown|mixed
	 */
	static function RFC3986urlencode ( $string )
	{
		if ($string === false)
		{
			return $string;
		}
		else
		{
			return str_replace(MasterCardSignatureInterceptor::ENCODED_TILDE, MasterCardSignatureInterceptor::TILDE, rawurlencode($string));
		}
	}

	/** Method to create all default parameters used in the base string and auth header
	 * @method Method to create all default parameters used in the base string and auth header
	 * @param config [config object holding data consumerKey, privateKey,..]
	 * @return array
	 */
	protected static function OAuthParametersFactory($config)
	{
		$nonce = self::generateNonce(16);
		//$configApi = MasterCardApiConfig::$config;
		$time = time();
		$params = array(
					MasterCardSignatureInterceptor::OAUTH_CONSUMER_KEY		=> $config->consumerKey,
					MasterCardSignatureInterceptor::OAUTH_SIGNATURE_METHOD	=> MasterCardSignatureInterceptor::signatureMethod,
					MasterCardSignatureInterceptor::OAUTH_NONCE				=> $nonce,
					MasterCardSignatureInterceptor::OAUTH_TIMESTAMP			=> $time,
					MasterCardSignatureInterceptor::OAUTH_VERSION			=> MasterCardSignatureInterceptor::version
					);

		return $params;
	}
}
?>