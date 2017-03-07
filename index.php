<?php
include_once "phar://MasterCardCoreSDK.phar/Interfaces/IApiTracker.php";
include_once "phar://MasterCardCoreSDK.phar/Interfaces/SDKConverter.php";
include_once "phar://MasterCardCoreSDK.phar/Interfaces/SDKErrorHandler.php";
include_once "phar://MasterCardCoreSDK.phar/ApiConfig.php";
include_once "phar://MasterCardCoreSDK.phar/ApiConfigBuilder.php";
include_once "phar://MasterCardCoreSDK.phar/baseSdkVersion.php";
include_once "phar://MasterCardCoreSDK.phar/MasterCardApiConfig.php";
include_once "phar://MasterCardCoreSDK.phar/Client/ApiClient.php";
include_once "phar://MasterCardCoreSDK.phar/Client/ApiTracker.php";
include_once "phar://MasterCardCoreSDK.phar/Converters/EncodedURLConverter.php";
include_once "phar://MasterCardCoreSDK.phar/Converters/SDKConverterFactory.php";
include_once "phar://MasterCardCoreSDK.phar/Converters/XMLConverter.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/MasterpassErrorHandler.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/SDKBaseException.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/SDKConversionException.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/SDKErrorResponseException.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/SDKOauthException.php";
include_once "phar://MasterCardCoreSDK.phar/Exception/SDKValidationException.php";
include_once "phar://MasterCardCoreSDK.phar/Helper/QueryParams.php";
include_once "phar://MasterCardCoreSDK.phar/Helper/ServiceRequest.php";
include_once "phar://MasterCardCoreSDK.phar/Interceptor/MasterCardAPITrackerInterceptor.php";
include_once "phar://MasterCardCoreSDK.phar/Interceptor/MasterCardSDKLoggingInterceptor.php";
include_once "phar://MasterCardCoreSDK.phar/Interceptor/MasterCardSignatureInterceptor.php";
include_once "phar://MasterCardCoreSDK.phar/model/AccessTokenResponse.php";

include_once "phar://MasterCardCoreSDK.phar/model/Detail.php";
include_once "phar://MasterCardCoreSDK.phar/model/Details.php";
include_once "phar://MasterCardCoreSDK.phar/model/Error.php";
include_once "phar://MasterCardCoreSDK.phar/model/Errors.php";
include_once "phar://MasterCardCoreSDK.phar/model/ExtensionPoint.php";
include_once "phar://MasterCardCoreSDK.phar/model/RequestTokenResponse.php";
include_once "phar://MasterCardCoreSDK.phar/model/SDKErrorReponse.php";
include_once "phar://MasterCardCoreSDK.phar/Services/AccessTokenApi.php";
include_once "phar://MasterCardCoreSDK.phar/Services/RequestTokenApi.php";
include_once "phar://MasterCardCoreSDK.phar/XML/Serializer.php";
include_once "phar://MasterCardCoreSDK.phar/XML/Unserializer.php";

?> 
