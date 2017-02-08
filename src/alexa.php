<?php
namespace Weilifang\AwisAlexa;

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
	 *
	 * @return void
	 */
    public function __construct($accessKeyId, $secretAccessKey)
    {
        //$this->httpClient = new Client();
        $this->accessKeyId = $accessKeyId;
        $this->secretAccessKey = $secretAccessKey;
    }

    /**
     * 取得传入域名的Alexa信息
     *
     *
     *
     */
	public function getUrlInfo($site, $responseGroup = "ContentData")
	{
		//传入参数,创建出参数数组
        $queryParams = $this->buildQueryParams($site, $responseGroup);
        //生成签名
        $sig = $this->generateSignature($queryParams);
        //组装请求字符串
        $url = 'http://' . $this->ServiceHost . '/?' . $queryParams . '&Signature=' . $sig;
        //发送请求，得到输出XML字符串
        $ret = self::makeRequest($url);

        //var_dump($ret);
        return $ret;
        //这里经常报错
        //self::parseResponse($ret);//解释XML
	}

    /**
     *
     *
     *
     *
     */
    protected function buildQuery($params)
    {
        return http_build_query($params);
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
            'SignatureVersion'  => $this->SigVersion,//签名版本
            'SignatureMethod'   => $this->HashAlgorithm,//签名方法
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
     * Generates an HMAC signature per RFC 2104.
     * 生成签名
     *
     * @param String $url URL to use in createing signature
     */
    protected function generateSignature($url)
	{
        $sign = "GET\n" . strtolower($this->ServiceHost) . "\n/\n" . $url;
        //echo "String to sign: \n" . $sign . "\n";
        $sig = base64_encode(hash_hmac('sha256', $sign, $this->secretAccessKey, true));
        //echo "\n 签名: " . $sig ."\n";

        return rawurlencode($sig);//rawurlencode — 按照 RFC 3986 对 URL 进行编码
    }

    /**
     * 向AWIS提出请求
     * [Makes request to AWIS]
     *
     * @param String $url URL to make request to
     * @return String Result of request
     */
    protected static function makeRequest($url)
	{
        //echo "\nMaking request to:\n$url\n";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
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
                'LinksInCount' => $info->ContentData->LinksInCount,
                'Rank'           => $info->TrafficData->Rank,
                'AdultContent'   => $info->ContentData->AdultContent,
                'Language'       => $info->ContentData->Language->Locale,
                'MedianLoadTime' => $info->ContentData->Speed->MedianLoadTime,//中间载入时间
                'Percentile'     => $info->ContentData->Speed->Percentile,//百分位数
            );
        }

        foreach ($nice_array as $k => $v) {
            echo $k . ': ' . $v ."\n";
        }
    }
}

