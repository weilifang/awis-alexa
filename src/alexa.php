<?php

class Alexa
{
    protected $accessKeyId;
    protected $secretAccessKey;

    protected $httpClient;

    public function __construct($accessKeyId, $secretAccessKey)
    {
        $this->httpClient = new Client();
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
    }

    /**
     * Builds query parameters for the request to AWIS.
     * Parameter names will be in alphabetical order and
     * parameter values will be urlencoded per RFC 3986.
     * @return String query parameters for the request
     */
    protected function buildQueryParams()
    {
        $params = array(
            'Action'            => self::$ActionName,
            'ResponseGroup'     => self::$ResponseGroupName,
            'AWSAccessKeyId'    => $this->accessKeyId,
            'Timestamp'         => self::getTimestamp(),
            'Count'             => self::$NumReturn,
            'Start'             => self::$StartNum,
            'SignatureVersion'  => self::$SigVersion,
            'SignatureMethod'   => self::$HashAlgorithm,
            'Url'               => $this->site
        );
        ksort($params);
        $keyvalue = array();
        foreach($params as $k => $v) {
            $keyvalue[] = $k . '=' . rawurlencode($v);
        }
        return implode('&', $keyvalue);
    }

    /**
     * Builds current ISO8601 timestamp.
     */
    protected static function getTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }
}

