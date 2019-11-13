<?php
namespace Weilifang\AwisAlexa;

/**
 * 蘑菇头alexa 2019-08月版本
 *
 *
 * 此示例将向Alexa Web信息服务发出请求 使用您的访问密钥ID和秘密访问密钥。
 *******************************************************************
 *
 * 方法一：获取Alexa
 *
 * 方法二：
 *
 * 方法三：
 *
 *
 *
 *
 *
 *
 */
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
	 * 请求访问哪个API接口
	 * @var string
	 */
	protected static $ActionName = 'UrlInfo';

    /**
     * URI
     *
     */
    protected static $ServiceURI = "/api";

    //范围
    protected static $ServiceRegion = "us-west-1";

    //服务名称
    protected static $ServiceName = "awis";

    //服务节点
    protected static $ServiceEndpoint = 'awis.us-west-1.amazonaws.com';

    protected static $NumReturn = 10;

    protected static $StartNum = 1;

    /**
     *
     *
     *
     *
     */
    protected $httpClient;

	/**
	 * 服务地址
	 *
     * protected static $ServiceHost = 'awis.amazonaws.com';
     *******************
	 * @var int
	 */
	protected $ServiceHost = 'awis.amazonaws.com';

    /**
     *
     *
     *
     */
	protected $HashAlgorithm = 'HmacSHA256';

	/**
	 * 版本选择
	 *
	 * @var int
	 */
	protected $SigVersion = '2';

	/**
	 * 类构造函数
     ************************************
     *
     *
     * $this->httpClient = new Client();
	 ******************************
	 * @return void
	 */
    public function __construct($accessKeyId, $secretAccessKey)
    {
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
        $now = time();
        $this->amzDate = gmdate("Ymd\THis\Z", $now);
        $this->dateStamp = gmdate("Ymd", $now);
    }

    /**
     * [方法一]取得传入域名的Alexa信息 (获取DomainAlexa给出的官方信息)
     *
     */
	public function getUrlInfo($site, $responseGroup = "ContentData")
	{
        $canonicalQuery = $this->buildQueryParams($site, $responseGroup);
        $canonicalHeaders = $this->buildHeaders(true);
        $signedHeaders = $this->buildHeaders(false);
        $payloadHash = hash('sha256', "");
        $canonicalRequest = "GET" . "\n" . self::$ServiceURI . "\n" . $canonicalQuery . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . $payloadHash;
        $algorithm = "AWS4-HMAC-SHA256";
        $credentialScope = $this->dateStamp . "/" . self::$ServiceRegion . "/" . self::$ServiceName . "/" . "aws4_request";
        $stringToSign = $algorithm . "\n" .  $this->amzDate . "\n" .  $credentialScope . "\n" .  hash('sha256', $canonicalRequest);
        $signingKey = $this->getSignatureKey();
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);
        $authorizationHeader = $algorithm . ' ' . 'Credential=' . $this->accessKeyId . '/' . $credentialScope . ', ' .  'SignedHeaders=' . $signedHeaders . ', ' . 'Signature=' . $signature;
        $url = 'https://' . $this->ServiceHost . self::$ServiceURI . '?' . $canonicalQuery;
        $ret = self::makeRequest($url, $authorizationHeader);

        return $ret;
	}

    /**
     * [方法二]
     * 获取接入的链接（外链）
     * SitesLinkingIn操作返回链接到给定网站的网站列表。在链接到网站的每个域中，仅返回单个链接 - 具有最高页面级别流量的链接。
     *
     * options
     * Count 每页要返回的最大结果数。请注意，响应文档可能包含的结果少于此最大值。默认值为“10”（最多20个）。
     *
     ****************************
     * @param $site 网站
     * @param $responseGroup SitesLinkingIn是唯一可用的值
     *
     *
     */
    public function getSitesLinkingIn($site, $options = ['start' => 0, 'count' => 20])
    {
        self::$ActionName = 'SitesLinkingIn';

        $responseGroup = "SitesLinkingIn";

        if ($options['count'] > self::$NumReturn) {
            self::$NumReturn = $options['count'];
        }

        if ($options['start'] != self::$StartNum) {
             self::$StartNum = $options['start'];
        }

        $canonicalQuery = $this->buildQueryParams($site, $responseGroup);
        $canonicalHeaders =  $this->buildHeaders(true);
        $signedHeaders = $this->buildHeaders(false);
        $payloadHash = hash('sha256', "");
        $canonicalRequest = "GET" . "\n" . self::$ServiceURI . "\n" . $canonicalQuery . "\n" . $canonicalHeaders . "\n" . $signedHeaders . "\n" . $payloadHash;
        $algorithm = "AWS4-HMAC-SHA256";
        $credentialScope = $this->dateStamp . "/" . self::$ServiceRegion . "/" . self::$ServiceName . "/" . "aws4_request";
        $stringToSign = $algorithm . "\n" .  $this->amzDate . "\n" .  $credentialScope . "\n" .  hash('sha256', $canonicalRequest);
        $signingKey = $this->getSignatureKey();
        $signature = hash_hmac('sha256', $stringToSign, $signingKey);
        $authorizationHeader = $algorithm . ' ' . 'Credential=' . $this->accessKeyId . '/' . $credentialScope . ', ' .  'SignedHeaders=' . $signedHeaders . ', ' . 'Signature=' . $signature;
        $url = 'https://' . $this->ServiceHost . self::$ServiceURI . '?' . $canonicalQuery;

        $ret = self::makeRequest($url, $authorizationHeader);

        return $ret;
    }

    /**
     * [方法三]
     * TrafficHistory操作返回每天返回4年的每日Alexa流量排名，每百万用户访问量和每百万用户的唯一页面浏览量。
     * 排名超过1,000,000的网站不包括在内。
     *
     *
     */
    public function getTrafficHistory()
    {
        //Range
    }

    /**
     * 创建请求字符串
     *
     *
     * @return string (aa=bb&cc=dd&ee=ff)
     */
    protected function buildQuery($params)
    {
        return http_build_query($params);
    }


    /**
     * Builds headers for the request to AWIS.
     * 创建 请求AWIS的头部信息
     * 生成两种头部信息：
     * 一种是常规头部信息
     * 一种是签名类头部信息
     *
     *-----------------------------
     * 示例：(空格只是为了美观)
     * 常规：host:awis.us-west-1.amazonaws.com \n x-amz-date:Ymd\THis\Z \n
     * 签名：awis.us-west-1.amazonaws.com; Ymd\THis\Z
     *********************************************
     * @param boolean $list true 就是生成常规的头部信息，反之false就是生成签名头部信息
     *
     * @return String headers for the request => 返回字符串还是每行带回车(\n)的头部
     */
    protected function buildHeaders($list)
    {
        $params = array(
            'host' => self::$ServiceEndpoint,
            'x-amz-date' => $this->amzDate
        );
        ksort($params);
        $keyvalue = array();
        foreach($params as $k => $v) {
            if ($list) {
                $keyvalue[] = $k . ':' . $v;
            } else {
              $keyvalue[] = $k;
            }
        }

        return ($list) ? implode("\n", $keyvalue) . "\n" : implode(';', $keyvalue);
    }

    /**
     * Builds query parameters for the request to AWIS.
	 * [构建查询参数] []
     *
     *
	 ********************************************************
     * Parameter names will be in alphabetical order and
     * parameter values will be urlencoded per RFC 3986.
     * @return String query parameters for the request(返回值示例：aa=bb&cc=dd&)
     */
    protected function buildQueryParams($site, $responseGroup)
    {
        $params = array(
            'Action'            => self::$ActionName,//接口方法
            'Count'             => self::$NumReturn,
            'ResponseGroup'     => $responseGroup,
            'Start'             => self::$StartNum,
            //'AWSAccessKeyId'    => $this->accessKeyId,//访问密钥 ID
            //'Timestamp'         => self::getTimestamp(),//得到时间戳
            //'Count'             => self::$NumReturn,
            //'Start'             => self::$StartNum,
            //'SignatureVersion'  => $this->SigVersion,//签名版本
            //'SignatureMethod'   => $this->HashAlgorithm,//签名方法
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
     * 签名相关函数
     * 只用于下面的 getSignatureKey 签名Key值用
     *
     */
    protected function sign($key, $msg)
    {
        return hash_hmac('sha256', $msg, $key, true);
    }

    /**
     * 签名相关函数
     *
     * 签名用的Key
     * 经过了层层的hash加密
     ******************************
     * @return string $kSigning
     */
    protected function getSignatureKey()
    {
        $kSecret = 'AWS4' . $this->secretAccessKey;
        $kDate = $this->sign($kSecret, $this->dateStamp);
        $kRegion = $this->sign($kDate, self::$ServiceRegion);
        $kService = $this->sign($kRegion, self::$ServiceName);
        $kSigning = $this->sign($kService, 'aws4_request');

        return $kSigning;
    }

    /**
     * 向AWIS提出请求
     *  [Makes request to AWIS]
     *
     *
     **************************************************
     * @param String $url URL to make request to
     * @param string $authorizationHeader 验证
     * @return String Result of request
     ************************************************
     * echo "\nMaking request to:\n$url\n";
     */
    protected function makeRequest($url, $authorizationHeader)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Accept: application/xml',
          'Content-Type: application/xml',
          'X-Amz-Date: ' . $this->amzDate,
          'Authorization: ' . $authorizationHeader
        ));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * 构建当前ISO8601时间戳
     * [Builds current ISO8601 timestamp.]
	 *
	 * @return string
     */
    protected static function getTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.\\0\\0\\0\\Z", time());
    }

    /**
     * 从AWIS解析XML响应并显示所选数据
     * [Parses XML response from AWIS and displays selected data]
     *
     * @param String $response xml response from AWIS
     */
    public static function parseResponse($response)
    {
        $xml = new \SimpleXMLElement($response, null, false, 'http://awis.amazonaws.com/doc/2005-07-11');
        if ($xml->count() && $xml->Response->UrlInfoResult->Alexa->count()) {
            $info = $xml->Response->UrlInfoResult->Alexa;
            $nice_array = array(
                'LinksInCount' => $info->ContentData->LinksInCount,//外链
                'Rank'           => $info->TrafficData->Rank,
                'AdultContent'   => $info->ContentData->AdultContent,
                'Language'       => $info->ContentData->Language->Locale,
                'MedianLoadTime' => $info->ContentData->Speed->MedianLoadTime,//中间载入时间
                'Percentile'     => $info->ContentData->Speed->Percentile,//百分位数
            );
        }

        return $nice_array;
    }
}

