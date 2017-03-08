<?php

namespace Masterpass\CoreSDK\Converters;

use Masterpass\CoreSDK\Exceptions\SDKConversionException;
use Masterpass\CoreSDK\Interfaces\SDKConverter;
use Masterpass\CoreSDK\XML\Serializer;
use DOMDocument;
use DOMXPath;
use Exception;
use Masterpass\CoreSDK\XML\Unserializer;

/**
 * XMLConverter  - XML request and response converter
 * @package MasterCardCoreSDK
 * @subpackage Converters
 * @implements SDKConverter
 */
class XMLConverter implements SDKConverter
{

    public $options = array(
        XML_SERIALIZER_OPTION_INDENT => '    ',
        XML_SERIALIZER_OPTION_RETURN_RESULT => true,
        "typeHints" => false,
        "addDecl" => true,
        XML_SERIALIZER_OPTION_CLASSNAME_AS_TAGNAME => 'true',
        'mode' => 'simplexml'
    );

    public $options_unserialize = array(
        // Defines whether nested tags should be returned as associative arrays or objects.
        XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'object',

        // use the tagname as the classname
        XML_UNSERIALIZER_OPTION_TAG_AS_CLASSNAME => true,

        // name of the class that is used to create objects
        XML_UNSERIALIZER_OPTION_DEFAULT_CLASS => 'stdClass',

        // specify the target encoding
        XML_UNSERIALIZER_OPTION_ENCODING_TARGET => "UTF-8",

        // unserialize() returns the result of the unserialization instead of true
        XML_UNSERIALIZER_OPTION_RETURN_RESULT => true,

        // remove whitespace around data
        XML_UNSERIALIZER_OPTION_WHITESPACE => XML_UNSERIALIZER_WHITESPACE_TRIM,

        XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => true
    );

    public function __construct()
    {

    }

    public function requestBodyConverter($objRequest)
    {
        $tagMapArray = array();

        try {
            if (!empty($objRequest)) {
                foreach ($objRequest as $key => $value) {
                    if (class_exists($key)) {
                        if (!empty($key::$attributeMap)) {
                            array_reverse($key::$attributeMap);
                            $tagMapArray = array_merge($tagMapArray, $key::$attributeMap);
                        }
                    }

                }
            }

            $this->options['XML_SERIALIZER_OPTION_TAGMAP'] = $tagMapArray;
            $serializer = new Serializer($this->options);
            $result = $serializer->serialize($objRequest);

            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->loadxml(utf8_encode($result));
            $xpath = new DOMXPath($doc);
            foreach ($xpath->query('//*[not(normalize-space())]') as $node) {
                $node->parentNode->removeChild($node);
            }
            $doc->formatOutput = true;
            $reqXML = $doc->savexml();

            return $reqXML;
        } catch (Exception $e) {
            throw new SDKConversionException($e, __class__);
        }
    }

    public function responseBodyConverter($xmlResponse, $responseType)
    {
        $tagMapArray = array();
        try {
            foreach ($responseType::$attributeMap as $key => $val) {
                if (class_exists($key)) {
                    if (!empty($key::$attributeMap)) {
                        $tagMapArray = array_merge($tagMapArray, $key::$attributeMap);
                    }
                }
            }

            $this->options_unserialize['XML_UNSERIALIZER_OPTION_DEFAULT_CLASS'] = $responseType;
            $this->options_unserialize['XML_UNSERIALIZER_OPTION_TAG_MAP'] = $tagMapArray;

            $unserializer = new Unserializer($this->options_unserialize);

            $result_unserialize = $unserializer->unserialize($xmlResponse, false, array(XML_UNSERIALIZER_OPTION_TAG_MAP => $tagMapArray));

            return $result_unserialize;
        } catch (Exception $e) {
            throw new SDKConversionException($e, __class__);
        }
    }
}

?>