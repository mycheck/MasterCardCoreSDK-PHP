<?php

namespace Masterpass\CoreSDK\Interceptors;

/**
 * Interceptor for to set SDK tracking information in each request header.
 * @package  MasterCardCoreSDK
 * @subpackage  Interceptor
 *
 */

class MasterCardAPITrackerInterceptor {

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
	 * Constructor, sets the implemented api tracker.
	 *
	 * @param apiTracker	the implemented APITracker reference.
	 */
	public function __construct($tracker)
	{
		$this->apiTrackerService = $tracker;
		$this->logger = Logger::getLogger(basename(__FILE__));
	}

	/**
	 * Adds API tracking and user agent headers & returns to Api Client
	 * @return api tracking info headers
	 */

	public function intercept()
	{
		$headers = ''; $trackingHeadrValue = ''; $headertracker = '';
		$headerVal = ''; $trackingBaseVersion = '';
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