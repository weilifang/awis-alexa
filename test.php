<?php
require_once 'src/alexa.php';

$awis = new Alexa('AKIAJBV37W6XMKGUTKPA', '+W3OmPXZ6WNydEK2TbRVYRE7pqc57/Lhex72iCmP');

$awis->getUrlInfo('http://www.baidu.com', 'Rank,LinksInCount,RankByCountry,UsageStats,AdultContent');//Rank,LinksInCount
