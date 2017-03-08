<?php

namespace Masterpass\CoreSDK\Interceptors;

use Logger;
use Masterpass\CoreSDK\baseSdkVersion;
use Masterpass\CoreSDK\Exceptions\SDKValidationException;

/**
 * Class MasterCardAPITrackerInterceptor
 * @package Masterpass\CoreSDK\Interceptors
 */
class MasterCardAPITrackerInterceptor
{

	/* @const Base sdk version */
	const BASE_SDK_VERSION='base_sdk_version=';

	const COMMA = ",";

	/*
	*@const Error Msg for User-Agent header null value
	*/
	const ERR_MSG_NULL_HEADR = "Found null value for User-Agent header!";/*


	*@const Error Msg for API Tracker service not set
	*/
	const ERR_MSG_NULL_SERVICE = "Found API tracker service is not implemented!";

	/*
	* @const User Agent Value
	*/
	const USER_AGENT = "User-Agent";

	private $apiTrackerService;

    /**
     * MasterCardAPITrackerInterceptor constructor.
     * @param $tracker
     */
	public function __construct($tracker)
	{
		$this->apiTrackerService = $tracker;
		$this->logger = Logger::getLogger(basename(__FILE__));
	}

    /**
     * @return string
     */
	public function intercept()
	{
		$headers = '';
//		$trackingHeadrValue = ''; $headertracker = '';
//		$headerVal = ''; $trackingBaseVersion = '';
		$headertracker = $this->apiTrackerService;

		if(empty($headertracker))
		{
			throw new SDKValidationException(MasterCardAPITrackerInterceptor::ERR_MSG_NULL_HEADR);
		}

		$baseSdkVer = baseSdkVersion::baseVersion;
		$trackingBaseVersion = self::BASE_SDK_VERSION.$baseSdkVer.self::COMMA;
		$headerVal = $headertracker->getAPITrackingHeader();
		$trackingHeadrValue = $trackingBaseVersion.$headerVal;

		$headers[MasterCardAPITrackerInterceptor::USER_AGENT] = $trackingHeadrValue;

		return $headers;
	}

}?>