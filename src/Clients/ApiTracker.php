<?php

namespace Masterpass\CoreSDK\Clients;

/**
 * @package  MasterCardCoreSDK
 * @subpackage Client
 * @category class ApiTracker
 */
//use Masterpass\CoreSDK\baseSdkVersion;
use Masterpass\CoreSDK\Interfaces\IApiTracker;
use Masterpass\CoreSDK\MasterCardApiConfig;

/**
 * Log API tracking information for request and access token api.
 *
 */
class ApiTracker implements IApiTracker
{
    /* @const Base sdk version */
    const BASE_SDK_VERSION = 'base_sdk_version=';

    /* @const language version */
    const LANG_VERSION = "lang_version=";

    /* @const language name */
    const PROGRAMMING_LANG = "lang_name=";

    /* @const language */
    const PHP = "PHP";

    const COMMA = ",";

    /* @const Plugin Version */
    const KEY_PLUGIN_VERSION = "plugin_version";

    const EQUAL = "=";

    public function getAPITrackingHeader()
    {
        $trackingHeader = '';
//        $baseSdkVer = baseSdkVersion::baseVersion;

//        $trackingHeader .= ApiTracker::BASE_SDK_VERSION . $baseSdkVer . ApiTracker::COMMA;
        $trackingHeader .= ApiTracker::PROGRAMMING_LANG . ApiTracker::PHP . ApiTracker::COMMA;
        $trackingHeader .= ApiTracker::LANG_VERSION . phpversion();

        if (!empty(MasterCardApiConfig::$additionalProperties)
            && array_key_exists(ApiTracker::KEY_PLUGIN_VERSION, MasterCardApiConfig::$additionalProperties)
        ) {
            $trackingHeader .= ApiTracker::COMMA . ApiTracker::KEY_PLUGIN_VERSION . ApiTracker::EQUAL . MasterCardApiConfig::$additionalProperties[ApiTracker::KEY_PLUGIN_VERSION];
        }

        return (string)$trackingHeader;
    }

    public function getUserAgentHeader()
    {
        return "MC Open API OAuth Framework v1.0-PHP";
    }
}
