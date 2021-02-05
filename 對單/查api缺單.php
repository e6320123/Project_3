-4 時區 都會刷 rpt
vs json縮排  點擊刪tab

1. 對照抓單與補單程式是否參數有對上  抓單程式不能改  只能改補單程式

3.phperr(error_log) 

xbblive  188為例
dc_24 -> /var/www/html/laravel/app/Custom/Repositories/API -> BBINXBBLive.php
function getBetRecord -> 
<?php

$param['pagelimit'] = '500';
//dump($param);
$keystr = $param['website'].$param['username'].$this->recordKey.date('Ymd');
$param['key'] = str_random(9).md5($keystr).str_random(5);
$url = $this->getApiUrl('WagersRecordBy75');
//print_r($url);
$res = $this->post_url($url,$param,'POST');
//dump($res);
//IntegrationApi::_print($res) ;
return json_decode($res);

?>
bbin平台後台查掉單

dc_24 -> php artisan BBIN 
用dump 跟 print_r 看request 跟回傳json
json內找無注單代表api沒傳
百科全書 -> 新人教學 -> 19 BBIN API

------------------------------------------
飛機 sup/mis/cs 回客服

bbin_01_08_188.txt檔案

請告知BBIN平台1/8 XBB真人注單API未回傳
188:64340761
b18:64340801
x88:64340792 , 64340729
xxc:64340773
這邊僅提供188的傳參和回傳 
請協助與平台方確認下 感謝