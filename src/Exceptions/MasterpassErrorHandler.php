<?php

namespace Masterpass\CoreSDK\Exceptions;

use Masterpass\CoreSDK\Converters\SDKConverterFactory;
use Masterpass\CoreSDK\Interfaces\SDKErrorHandler;
use Masterpass\CoreSDK\Models\Error;
use Masterpass\CoreSDK\Models\Errors;
use Logger;

/**
 * Class MasterpassErrorHandler
 * @package Masterpass\CoreSDK\Exceptions
 */
class MasterpassErrorHandler implements SDKErrorHandler
{
    public $logger;
    const ERR_DESC_NULL_RES = "Received null response: ";
    const ERR_DESC_EMPTY_RES = "Received empty body: ";
    const ERR_REASON_NULL_RES = "NULL_RESPONSE";
    const ERR_REASON_EMPTY_BODY_RES = "EMPTY_BODY";
    const ERR_REASON_INVALID_RES = "INVALID_RESPONSE";
    const ERR_REASON_NT_TIMEOUT = "NETWORK_TIMEOUT";
    const ERR_MSG_INVALID_RES = "Received invalid response: [";
    const ERR_MSG_UNKN_RES = "Unknown reason for failure, please check logs.";
    const ERR_SRC_UNKN = "Unknown";
    const ERR_NETWORK = "NETWORK_ERROR";
    const ERR_UNKN_REASON = "GENERAL_ERROR";
    const ERR_INTERNAL = "INTERNAL_PROCESSING_ERROR";
    const SEP_COLON = "]: ";
    const ERR_MSG_RES_PARSE = "Exception occurred during response parsing.";
    const ERR = "There is an error";
    const CONTENT_TYPE = "Content-Type";

    public function __construct()
    {
        $this->logger = Logger::getLogger(basename(__FILE__));
    }

    public function handleError($sdkErrorResponse)
    {
        $errors = '';
        $response = '';
        $responseBody = '';
        $response = $sdkErrorResponse->getResponse();
        if ($response) {
            $responseBody = $response->getBody();
            $responseStatusCode = $response->getStatusCode();
        } else if ($response == null) {
            $errors = $this->getErrorsObj(MasterpassErrorHandler::ERR, MasterpassErrorHandler::ERR_REASON_NULL_RES,
                $sdkErrorResponse->getErrorSource());
            throw new SDKErrorResponseException($errors, 400);
        }

        if ($responseBody == null) {
            $errors = $this->getErrorsObj(MasterpassErrorHandler::ERR_DESC_EMPTY_RES, MasterpassErrorHandler::ERR_REASON_EMPTY_BODY_RES, MasterpassErrorHandler::ERR_SRC_UNKN);
            throw new SDKErrorResponseException($errors, $responseStatusCode);
        }

        $contentTypeRes = $response->getHeader(MasterpassErrorHandler::CONTENT_TYPE);
        $contentType = explode("/", $contentTypeRes[0]);
        if ($responseStatusCode == 200) {
            try {
                $converter = SDKConverterFactory::getConverter(strtoupper($contentType[1]));
                $sdkErrorResponse->setErrorSource(strtoupper($contentType[1]) . " Converter");
                $errors = $converter->responseBodyConverter($response->getBody(), "Errors");
            } catch (SDKConversionException $sdkConversionException) {
                // if application content type in response is other than mentioned in sdk convertor factory for e.g application/xml;charset="ISO-..";
                $description = MasterpassErrorHandler::ERR_MSG_INVALID_RES . $responseBody . MasterpassErrorHandler::SEP_COLON;
                $errors = $this->getErrorsObj($description, MasterpassErrorHandler::ERR_REASON_INVALID_RES, $sdkConversionException->getConverterName());
                throw new SDKErrorResponseException($errors, $responseStatusCode);
            }
        } else {
            $description = MasterpassErrorHandler::ERR_MSG_INVALID_RES . $responseBody . MasterpassErrorHandler::SEP_COLON;
            $errors = $this->getErrorsObj($description, MasterpassErrorHandler::ERR_REASON_INVALID_RES, $sdkErrorResponse->getErrorSource());
            throw new SDKErrorResponseException($errors, 400);
        }
    }

    /**
     * @param $description
     * @param $reasonCode
     * @param $errSource
     * @return mixed
     */
    private function getErrorsObj($description, $reasonCode, $errSource)
    {
        $error = new Error();
        $error->Description = $description;
        $error->ReasonCode = $reasonCode;
        $error->Source = $errSource;
        $error->Recoverable = false;
        $errors = new Errors();
        $errors->Error = $error;
        return $errors;
    }
}
