<?php

namespace Masterpass\CoreSDK;

/**
 * Class ApiConfigBuilder
 * @package Masterpass\CoreSDK
 */
class ApiConfigBuilder
{
    public $hostUrl;
    public $envName;
    public $consumerKey;
    public $privateKey;
    const ERR_MISSING_DATA = "Missing Required Data ";

    /**
     * @param $envName
     */
    public function setEnvName($envName)
    {
        $this->envName = $envName;
    }

    /**
     * @param $hostUrl
     */
    public function setHostUrl($hostUrl)
    {

        $this->hostUrl = $hostUrl;
    }

    /**
     * @param $consumerKey
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @param $privateKey
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return ApiConfig
     */
    public function build()
    {
        $config = new ApiConfig($this->envName, $this->hostUrl, $this->consumerKey, $this->privateKey);
        MasterCardApiConfig::registerConfig($config);
        return $config;
    }
}
