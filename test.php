<?php
require_once 'src/alexa.php';

$awis = new Alexa('AKIAJBV37W6XMKGUTKPA', '+W3OmPXZ6WNydEK2TbRVYRE7pqc57/Lhex72iCmP');

$result = $awis->getUrlInfo('http://www.baidu.com', 'Rank,LinksInCount,RankByCountry,UsageStats,AdultContent,Language,Speed,OwnedDomains');//Rank,LinksInCount

$data = $awis->parseResponse($result);

//
//可以使用的字段有如下：
/**
 * RelatedLinks:相关链接。 Up to 11 related links 最多11个相关链接
 * Categories:类别。
 * Rank:排名。 Alexa三个月平均流量排名
 *
 * RankByCountry:
 * UsageStats: 。 使用情况统计信息（如覆盖面和网页浏览量）
 * AdultContent:成人内容。 网站是否可能包含成人内容（“是”或“否”）
 * Speed:速度。 平均加载时间和已知站点的百分比较慢
 * Language:语言。 内容语言代码和字符编码（请注意，这可能不匹配网站上任何给定页面的语言或字符编码，因为返回的语言和字符集是大多数页面的语言或字符集）
 * OwnedDomains: 。 这一个网站同一拥有者的其他域名
 * LinksInCount:链接数量。 指向此网站的链接数
 * SiteData:网站数据。 创建网站的标题，说明和日期
 */








