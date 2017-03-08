<?php

namespace Masterpass\CoreSDK;

use Masterpass\CoreSDK\Exceptions\SDKValidationException;

class MasterCardApiConfig
{
    public static $consumerKey;
    public static $privateKey;
    public static $sandbox = true;
    public static $additionalProperties = array();

    /**
     * @var ApiConfigBuilder
     */
    public static $sandboxBuilder;

    /**
     * @var ApiConfigBuilder
     */
    public static $productionBuilder;

    const PRODUCTION = "PRODUCTION";
    const SANDBOX = "SANDBOX";

    const SANDBOX_URL = "https://sandbox.api.mastercard.com";
    const PROD_URL = "https://api.mastercard.com";
    public static $configs = array();
    public static $builders = array();

    public static function setPrivateKey($privateKey)
    {
        MasterCardApiConfig::$privateKey = $privateKey;
    }

    public static function getConsumerKey()
    {
        return MasterCardApiConfig::$consumerKey;
    }

    public static function setConsumerKey($consumerKey)
    {
        MasterCardApiConfig::$consumerKey = $consumerKey;
    }

    public static function isSandBox()
    {
        return MasterCardApiConfig::$sandbox;
    }

    public static function hostUrl()
    {
        if (MasterCardApiConfig::$sandbox) {
            return MasterCardApiConfig::SANDBOX_URL;
        } else {
            return MasterCardApiConfig::PROD_URL;
        }
    }

    public static function setSandBox($sandbox)
    {
        MasterCardApiConfig::$sandbox = $sandbox;
    }

    public function getAdditionalProperties()
    {
        return MasterCardApiConfig::$additionalProperties;
    }

    public function setAdditionalProperties($dataArray)
    {
        MasterCardApiConfig::$additionalProperties = $dataArray;
    }

    public static function validateConfig($config)
    {
        if (empty($config->privateKey)) {
            throw new SDKValidationException(SDKValidationException::ERR_MSG_PRIVATE_KEY);
        }

        if (empty($config->consumerKey)) {
            throw new SDKValidationException(SDKValidationException::ERR_MSG_CONSUMER_ID);
        }
    }


// ==============  Multiconfig Changes ======================

    public static function registerConfig(ApiConfig $apiConfig)
    {

        $envNm = $apiConfig->getEnvName();
        MasterCardApiConfig::$configs[$envNm] = $apiConfig;

    }

    public static function getConfig($name = NULL)
    {
        if (!empty($name)) {
            $apiConfig = MasterCardApiConfig::$configs[$name];
            return $apiConfig;
        } else {
            if (MasterCardApiConfig::$sandbox) {
                MasterCardApiConfig::$sandboxBuilder->privateKey = MasterCardApiConfig::$privateKey;
                MasterCardApiConfig::$sandboxBuilder->consumerKey = MasterCardApiConfig::$consumerKey;
                return MasterCardApiConfig::$sandboxBuilder->build();
            } else {
                MasterCardApiConfig::$productionBuilder->setPrivateKey(MasterCardApiConfig::$privateKey);
                MasterCardApiConfig::$productionBuilder->setConsumerKey(MasterCardApiConfig::$consumerKey);
                return MasterCardApiConfig::$productionBuilder->build();
            }
        }
    }
}

MasterCardApiConfig::$productionBuilder = new ApiConfigBuilder();
MasterCardApiConfig::$productionBuilder->setEnvName(MasterCardApiConfig::PRODUCTION);
MasterCardApiConfig::$productionBuilder->setHostUrl(MasterCardApiConfig::PROD_URL);

MasterCardApiConfig::$sandboxBuilder = new ApiConfigBuilder();
MasterCardApiConfig::$sandboxBuilder->envName = MasterCardApiConfig::SANDBOX;
MasterCardApiConfig::$sandboxBuilder->hostUrl = MasterCardApiConfig::SANDBOX_URL;

MasterCardApiConfig::$builders = array
(
    MasterCardApiConfig::SANDBOX => MasterCardApiConfig::$sandboxBuilder,
    MasterCardApiConfig::PRODUCTION => MasterCardApiConfig::$productionBuilder
);


?>