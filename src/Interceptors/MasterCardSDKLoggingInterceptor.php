<?php

namespace Masterpass\CoreSDK\Interceptors;

class MasterCardSDKLoggingInterceptor
{

	const REQUEST_HEADERS = "Request Headers : ";
	const RESPONSE_STATUS = "Response Status : ";
	const REQUEST_INFO = "Request Information  ";
	const RESPONSE_INFO = "Response Information  ";
	const RESPONSE_REASON = "Response : ";
	const RESPONSE_BODY = "Response Body : ";
	const XML = "XML";

    public function __construct()
    {
        $this->logger = Logger::getLogger(basename(__FILE__));
    }

	public function requestLog($url,$headers,$result)
	{
		$headerString= ''; $requestString = '';
		foreach($headers as $key=>$value)
		{
			$headerString .= $key.": ".$value. ";\r\n";
		}
		$requestString = MasterCardSDKLoggingInterceptor::REQUEST_INFO. "\r\n". $url. "\r\n". $headerString;

		if($result!='')
		{
			$requestString .= $result;
		}
		$this->logger->debug($requestString);
	}

	public function responseLog($url,$res)
	{
		$responseString = ''; $statusCode = '';
		$responseString .= " ".MasterCardSDKLoggingInterceptor::RESPONSE_INFO."\r\n".$url. " ";
		$responseString .= "\r\n".MasterCardSDKLoggingInterceptor::RESPONSE_REASON." ". $res->getReasonPhrase();

		$contentTypeRes = $res->getHeader ( 'Content-Type' );

		if (strpos ( $contentTypeRes [0], ";" )) {
			$contentType = explode ( ";", $contentTypeRes [0] );
			$contentTypeVal = explode ( "/", $contentType [0] );
		} else {
			$contentTypeVal = explode ( "/", $contentTypeRes [0] );
		}

		$xmlData = $res->getBody();
		if(strtoupper($contentTypeVal[1])==self::XML)
		{
			if($xmlData != strip_tags($xmlData))
			{
				$dom = new DOMDocument();
				$dom->loadXML($xmlData);
				foreach ($dom->documentElement->childNodes as $node)
				{
					if($node->nodeType==1)
					{
						$oldAccNum = $node->getElementsByTagName('AccountNumber')->Item(0);
						if(isset($oldAccNum->parentNode))
						{
							$parentNode = $oldAccNum->parentNode;
							if($parentNode->nodeName=="Card")
							{
								if(!empty($oldAccNum->nodeValue) && strlen($oldAccNum->nodeValue)>=12)
								{
									$accNum = $oldAccNum->nodeValue;
									$starred = substr_replace($accNum, str_repeat('*', strlen($accNum) - 4), 0, -4);
									$newAccNum = $dom->createElement('AccountNumber',$starred);
									$oldAccNum->parentNode->replaceChild($newAccNum, $oldAccNum);
								}
							}
						}
					}
				}
				$strXmlRes = $dom->saveXML($dom->documentElement);
			}
		}
		else {
			$strXmlRes = $res->getBody();
		}

		$responseString .= "\r\n".MasterCardSDKLoggingInterceptor::RESPONSE_BODY." ".$strXmlRes;

		$this->logger->debug($responseString);
		$statusCode = $res->getStatusCode();
		$this->logger->info(MasterCardSDKLoggingInterceptor::RESPONSE_STATUS.$statusCode." ;".MasterCardSDKLoggingInterceptor::RESPONSE_REASON." ".$res->getReasonPhrase());
	}

}?>