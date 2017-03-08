<?php

namespace Masterpass\CoreSDK\Clients;

use Guzzle\Http\Client;
use Masterpass\CoreSDK\Converters\SDKConverterFactory;
use Masterpass\CoreSDK\Interceptors\MasterCardAPITrackerInterceptor;
use Masterpass\CoreSDK\Interceptors\MasterCardSDKLoggingInterceptor;
use Masterpass\CoreSDK\Interceptors\MasterCardSignatureInterceptor;
use Masterpass\CoreSDK\MasterCardApiConfig;
use Masterpass\CoreSDK\Exceptions\SDKValidationException;

class ApiClient
{
	public $logger;
	public $sdkErrorHandler;
	private $apiTrackrInterObj;
	const ERR_HANDLER_NOT_FOUND = "SDK Error Handler Not Found";
	const ERR_RESPONSE_CODE = "Error Response Code : ";
	public $configApi;

    public function __construct($apiConfig=null)
    {
		$this->logger = Logger::getLogger(basename(__FILE__));
		try
		{
			if (empty ( $apiConfig )) {
				$this->configApi = MasterCardApiConfig::getConfig ();
			} else {
				$this->configApi = $apiConfig;
			}

			MasterCardApiConfig::validateConfig ( $this->configApi );
		}
		catch(SDKValidationException $e)
		{
			$this->logger->error($e->getMessage());
			throw new SDKValidationException($e->getMessage());
		}

    }

	public function call($resourcePath,$serviceRequest,$method,$responseType)
    {
        $headers = array();

        $url = $this->configApi->hostUrl . $resourcePath;

		$pathParams = $serviceRequest->getPathParams();
		if(count($pathParams)>0)
		{
			foreach($pathParams as $key=>$value)
			{
				$placeholder = sprintf('{%s}', $key);
				$url  = str_replace($placeholder, $value, $url);
			}
		}

		$this->logger->info($method." ".$url);
		$reqContentType = $serviceRequest->getContentType();
		if(strpos($reqContentType,";"))
			{
				$contentType = explode(";",$reqContentType);
				$contentTypeVal = explode("/",$contentType[0]);
			} else
			{
				$contentTypeVal = explode("/",$reqContentType);
			}

		$converter = SDKConverterFactory::getConverter(strtoupper($contentTypeVal[1]));
		$result = $converter->requestBodyConverter($serviceRequest->getRequestBody());

		$reqHeaders = $serviceRequest->getHeaders();
		$reqContentType = $serviceRequest->getContentType();
		$headers = MasterCardSignatureInterceptor::getReqHeaders($url,$method,$result,$serviceRequest,$this->configApi);

		$this->logger->info(MasterCardSignatureInterceptor::AUTH_HEADER_INFO);
		$cliTracker = $this->apiTrackrInterObj;

		if(empty($cliTracker))
		{
			throw new SDKValidationException(MasterCardAPITrackerInterceptor::ERR_MSG_NULL_SERVICE);
		}

		$apiTrackerHeader  = $cliTracker->intercept();
		$headers = array_merge($headers,$apiTrackerHeader);

		try
		{
			// Logging Request
			MasterCardSDKLoggingInterceptor::requestLog($method." ".$url,$headers,$result);

			$client = new Client();
			$res = $client->request($method, $url ,['verify'=>false,'headers' => $headers,'body' => $result]);
			$statusCode = $res->getStatusCode();

			$contentTypeRes = $res->getHeader ( 'Content-Type' );

			if (strpos ( $contentTypeRes [0], ";" )) {
				$contentType = explode ( ";", $contentTypeRes [0] );
				$contentTypeVal = explode ( "/", $contentType [0] );
			} else {
				$contentTypeVal = explode ( "/", $contentTypeRes [0] );
			}

			// Logging Response
			MasterCardSDKLoggingInterceptor::responseLog ( $url, $res);

			$converter = SDKConverterFactory::getConverter ( strtoupper ( $contentTypeVal [1] ) );
			$result_unserialize = $converter->responseBodyConverter ($res->getBody(), $responseType );

		}
		catch(SDKConversionException $e)
		{
			$this->logger->error($e->getConverterName());
			$sdkErrorResponse = new SDKErrorResponse($res,$statusCode);
			if($this->sdkErrorHandler != null)
			{
				$this->sdkErrorHandler->handleError($sdkErrorResponse);
			}
			else
			{
			   throw new SDKBaseException(ApiClient::ERR_HANDLER_NOT_FOUND);
			}
		return $result_unserialize;
		}
		catch (Exception $e)
		{
			$response = $e->getResponse();
			$statusCode = $response->getStatusCode();
			$this->logger->info(ApiClient::ERR_RESPONSE_CODE.$statusCode);

			// Logging Response
			MasterCardSDKLoggingInterceptor::responseLog($url,$response);


			//	call handler and throw
			$sdkErrorResponse = new SDKErrorResponse($response,$statusCode);
				if($this->sdkErrorHandler != null)
				{
					$this->sdkErrorHandler->handleError($sdkErrorResponse);
				}
				else
				{
				   throw new SDKBaseException(ApiClient::ERR_HANDLER_NOT_FOUND);
				}
			return $result_unserialize;
		}
      return $result_unserialize;
    }

	/**
	 * Set user defined APItracker implementation
	 * @param tracker
	 */
	public function setApiTracker($apiTracker)
	{
	  $this->apiTrackrInterObj = new MasterCardAPITrackerInterceptor($apiTracker);
	}
}
?>