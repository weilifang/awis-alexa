<?php

class Alexa
{
	/**
	 * 访问密钥 ID
	 *
	 * @var string
	 */
    protected $accessKeyId;
	
	/**
	 * 私有访问密钥
	 *
	 * @var string
	 */
    protected $secretAccessKey;
	
	/**
	 * 请求API的方法名称
	 *
	 * @var string
	 */
	protected static $ActionName = 'UrlInfo';

    protected $httpClient;
	
	
	/**
	 * 服务地址
	 *
	 * @var int
	 */
	protected $ServiceHost = 'awis.amazonaws.com';
	
	protected $HashAlgorithm = 'HmacSHA256';
	
	/**
	 * 版本选择
	 *
	 * @var int
	 */
	protected $SigVersion = '2';

	/**
	 * 类构造函数
	 *
	 * @return void
	 */
    public function __construct($accessKeyId, $secretAccessKey)
    {
        //$this->httpClient = new Client();
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
    }
	
	public function getUrlInfo($site, $responseGroup = "ContentData")
	{
		//$this->site = $site;
		
        $queryParams = $this->buildQueryParams($site, $responseGroup);
		
		
        $sig = $this->generateSignature($queryParams);
		
		
		
        $url = 'http://' . $this->ServiceHost . '/?' . $queryParams . 
            '&Signature=' . $sig;
		
		
        $ret = self::makeRequest($url);
		var_dump($ret);
		
		
        echo "\nResults for " . $site .":\n\n";
		
        self::parseResponse($ret);
	}

    /**
     * Builds query parameters for the request to AWIS.
	 * [构建查询参数] []
	 *
     * Parameter names will be in alphabetical order and
     * parameter values will be urlencoded per RFC 3986.
     * @return String query parameters for the request 
     */
    protected function buildQueryParams($site, $responseGroup)
    {
        $params = array(
            'Action'            => self::$ActionName,//接口方法 
            'ResponseGroup'     => $responseGroup,
            'AWSAccessKeyId'    => $this->accessKeyId,//访问密钥 ID
            'Timestamp'         => self::getTimestamp(),//得到时间戳
            //'Count'             => self::$NumReturn,
            //'Start'             => self::$StartNum,
            'SignatureVersion'  => $this->SigVersion,
            'SignatureMethod'   => $this->HashAlgorithm,
            'Url'               => $site,
        );
        ksort($params);//数组排序
        $keyvalue = array();
        foreach($params as $k => $v) {
            $keyvalue[] = $k . '=' . rawurlencode($v);
        }
        return implode('&', $keyvalue);
    }
	
    /**
     * Parses XML response from AWIS and displays selected data
     * @param String $response    xml response from AWIS
     */
    public static function parseResponse($response) {
        $xml = new SimpleXMLElement($response, null, false, 'http://awis.amazonaws.com/doc/2005-07-11');
        if ($xml->count() && $xml->Response->UrlInfoResult->Alexa->count()) {
            $info = $xml->Response->UrlInfoResult->Alexa;
            $nice_array = array(
                'Links In Count' => $info->ContentData->LinksInCount,
                'Rank'           => $info->TrafficData->Rank
            );
        }
        foreach ($nice_array as $k => $v) {
            echo $k . ': ' . $v ."\n";
        }
    }

    /**
     * Generates an HMAC signature per RFC 2104.
     *
     * @param String $url       URL to use in createing signature
     */
    protected function generateSignature($url) 
	{
        $sign = "GET\n" . strtolower($this->ServiceHost) . "\n/\n" . $url;
		
		
        echo "String to sign: \n" . $sign . "\n";
		
		
        $sig = base64_encode(hash_hmac('sha256', $sign, $this->secretAccessKey, true));
		
		
        echo "\nSignature: " . $sig ."\n";
		
        return rawurlencode($sig);//rawurlencode — 按照 RFC 3986 对 URL 进行编码
    }

    /**
     * Makes request to AWIS
     * @param String $url   URL to make request to
     * @return String       Result of request
     */
    protected static function makeRequest($url) 
	{
        echo "\nMaking request to:\n$url\n";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Builds current ISO8601 timestamp.
	 * [构建当前ISO8601时间戳]
	 *
     */
    protected static function getTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }
}

