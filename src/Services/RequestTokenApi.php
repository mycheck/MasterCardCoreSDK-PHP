<?php

namespace Masterpass\CoreSDK\Services;
use Masterpass\CoreSDK\Clients\ApiClient;
use Masterpass\CoreSDK\Clients\ApiTracker;
use Masterpass\CoreSDK\Exceptions\MasterpassErrorHandler;
use Masterpass\CoreSDK\Helpers\ServiceRequest;

/**
 * Invokes RequestTokenApi
 * @package  MasterCardCoreSDK
 * @subpackage  Services
 */
 class RequestTokenApi {


     /**
      * @param $oauthCallbackURL
      * @param null $configName
      * @return mixed
      */
	public static function create($oauthCallbackURL, $configName = NULL ){

        $path = "/oauth/consumer/v1/request_token";

        $serviceRequest = new ServiceRequest();

        $serviceRequest->header("oauth_callback", $oauthCallbackURL);
        $serviceRequest->contentType("application/xml");

        $apiClient = new ApiClient($configName);
  		$apiClient->setApiTracker(new ApiTracker());
		$apiClient->sdkErrorHandler = new MasterpassErrorHandler();

        return $apiClient->call($path, $serviceRequest,"POST","RequestTokenResponse");
    }
}
?>