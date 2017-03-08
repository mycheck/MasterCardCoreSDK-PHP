<?php

namespace Masterpass\CoreSDK;

/**
 * Class ApiConfig
 * @package Masterpass\CoreSDK
 */
class ApiConfig
{
	public  $hostUrl;
	public  $consumerKey;
	public  $privateKey;
	public  $envName;

    /**
     * ApiConfig constructor.
     * @param $envName
     * @param $hostUrl
     * @param $consumerKey
     * @param $privateKey
     */
	public function __construct($envName,$hostUrl,$consumerKey,$privateKey) {
		$this->envName = $envName;
		$this->hostUrl = $hostUrl;
		$this->consumerKey = $consumerKey;
		$this->privateKey = $privateKey;
	}

    /**
     * @param $envName
     */
	public function setEnvName($envName) {
        $this->envName = $envName;
    }

    /**
     * @return mixed
     */
    public function getEnvName(){
        return $this->envName;
    }

    /**
     * @param $hostUrl
     */
	public function setHostUrl($hostUrl) {
        $this->hostUrl = $hostUrl;
    }

    /**
     * @return mixed
     */
    public function getHostUrl(){
        return $this->hostUrl;
    }

    /**
     * @param $consumerKey
     */
	public function setConsumerKey($consumerKey) {
		$this->consumerKey = $consumerKey;
	}

    /**
     * @return mixed
     */
	public function getConsumerKey() {
		return $this->consumerKey;
	}

    /**
     * @param $privateKey
     */
	public function setPrivateKey($privateKey) {
		$this->privateKey = $privateKey;
	}

    /**
     * @return mixed
     */
	public function getPrivateKey() {
		return $this->privateKey;
	}

}?>