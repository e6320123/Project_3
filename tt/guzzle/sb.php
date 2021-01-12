<?php
//require_once('../../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
require_once 'class.tools.php';

class sb extends tools{

    public function index($s_date,$e_date){

        for($account=1 ;$account<=3;$account++){

            $st = date("m/d/Y", strtotime($s_date));
            $en = date("m/d/Y", strtotime($e_date));

            $prefix = $this->prefixlist();

            $jar=new CookieJar;

            //取得登入頁
            switch ($account){
                case 1:
                    $line='98ywj6n';
                    $post = array(
                        'UserName'=>'BGCAIWU',
                        'Password'=>'ZCFYmFQTD0WL',
                    );
                    break;
                case 2:
                    $line='ad3b7bc';
                    $post = array(
                        'UserName'=>'BOSCS',
                        'Password'=>'tntC3HsfzdGi',
                    );
                    break;
                case 3:
                    $line='cjbgwue';
                    $post = array(
                        'UserName'=>'bscs',
                        'Password'=>'fmGHc7wJG2sL',
                    );
                    break;
                default:
                    continue 2;
            }

            $login_url = 'http://portal8.bpadmin999.com/' . $line . '/Auth/Login?Language=cs';

            $header = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            ];

            $login = $this->bodytool($login_url, $header, 'POST', $jar, $post);
            //取得token值
            $token_url = 'http://portal8.bpadmin999.com/' . $line . '/Verify/ApplyToken?ActionLink=CustomerWinlossReport&Type=1';
            $tmp_token = $this->bodytool($token_url, $header, 'GET', $jar, null);
            $dom = new DOMDocument();
            @$dom->loadHTML($tmp_token);
            $xpath = new DOMXpath($dom);
            $query = $xpath->query("//input[@name='__RequestVerificationToken']");
            foreach ($query as $key => $value) {
                $token[$key] = $value->getAttribute('value');
            }
            //取得比數
            $num_url = 'http://lap.bpadmin999.com/CustomerWinLossReport/Index';
            $post = array(
                '__RequestVerificationToken'=>$token[1],
                'DateRange'=>$s_date.'-'.$e_date,
                'FromDate'=>$s_date,
                'ToDate'=>$e_date,
                'CurrencyName'=>'CNY',
                'CurrencyId'=>'13',
                'BaseCurrency'=>'',
                'Username'=>'',
                'SB'=>'SB',
                'NG'=>'NG',
                'BA'=>'BA',
                'VS'=>'VS',
                'KN'=>'KN',
                'VS2'=>'VS2',
                'ProductOptions[0].Code'=>'SB',
                'ProductOptions[0].Name'=>'体育博彩',
                'ProductOptions[0].Status'=>'true',
                'ProductOptions[1].Code'=>'NG',
                'ProductOptions[1].Name'=>'百练赛',
                'ProductOptions[1].Status'=>'true',
                'ProductOptions[2].Code'=>'BA',
                'ProductOptions[2].Name'=>'BA',
                'ProductOptions[2].Status'=>'true',
                'ProductOptions[3].Code'=>'VS',
                'ProductOptions[3].Name'=>'虚拟运动',
                'ProductOptions[3].Status'=>'true',
                'ProductOptions[4].Code'=>'KN',
                'ProductOptions[4].Name'=>'快乐彩',
                'ProductOptions[4].Status'=>'true',
                'ProductOptions[5].Code'=>'VS2',
                'ProductOptions[5].Name'=>'虚拟运动2',
                'ProductOptions[5].Status'=>'true',
                'ProductOptions[6].Code'=>'CC',
                'ProductOptions[6].Name'=>'乐卡迪亚',
                'ProductOptions[6].Status'=>'true',
                'IsExcludedTestData'=>'true',
                'PageIndex'=>'1',
                'PageSize'=>'500',
                'OrderItem'=>'2',
                'IsAscSort'=>'true',
            );

            $num = $this->bodytool($num_url, $header, 'POST', $jar, $post);
            $num = json_decode($num,true);
            $data_url = 'http://lap.bpadmin999.com/CustomerWinLossReport/Index';
            $post = array(
                '__RequestVerificationToken'=>$token[1],
                'DateRange'=>$s_date.'-'.$e_date,
                'FromDate'=>$s_date,
                'ToDate'=>$e_date,
                'CurrencyName'=>'CNY',
                'CurrencyId'=>'13',
                'BaseCurrency'=>'',
                'Username'=>'',
                'SB'=>'SB',
                'NG'=>'NG',
                'BA'=>'BA',
                'VS'=>'VS',
                'KN'=>'KN',
                'VS2'=>'VS2',
                'ProductOptions[0].Code'=>'SB',
                'ProductOptions[0].Name'=>'体育博彩',
                'ProductOptions[0].Status'=>'true',
                'ProductOptions[1].Code'=>'NG',
                'ProductOptions[1].Name'=>'百练赛',
                'ProductOptions[1].Status'=>'true',
                'ProductOptions[2].Code'=>'BA',
                'ProductOptions[2].Name'=>'BA',
                'ProductOptions[2].Status'=>'true',
                'ProductOptions[3].Code'=>'VS',
                'ProductOptions[3].Name'=>'虚拟运动',
                'ProductOptions[3].Status'=>'true',
                'ProductOptions[4].Code'=>'KN',
                'ProductOptions[4].Name'=>'快乐彩',
                'ProductOptions[4].Status'=>'true',
                'ProductOptions[5].Code'=>'VS2',
                'ProductOptions[5].Name'=>'虚拟运动2',
                'ProductOptions[5].Status'=>'true',
                'ProductOptions[6].Code'=>'CC',
                'ProductOptions[6].Name'=>'乐卡迪亚',
                'ProductOptions[6].Status'=>'true',
                'IsExcludedTestData'=>'true',
                'PageIndex'=>'1',
                'PageSize'=>$num['totalRows'],
                'OrderItem'=>'2',
                'IsAscSort'=>'true',
            );
            $data = $this->bodytool($data_url, $header, 'POST', $jar, $post);
            $data = json_decode($data,true);

            $spec_code = array('a852000a','hyeongoon','zhxhruc','q79548088','lpk0609','ssyuanyu','zyanhao','chenlei30','gang3143','zhangxueyou','test987');
            foreach ($prefix as $p_key => $p_value) {
                foreach ($data['dataList'] as $sb_key => $sb_value) {
                    if(strtolower(substr($sb_value['externalId'], 0, strlen("BET88"))) == "bet88")
                    {
                        continue;
                    }
                    if(strtolower(substr($sb_value['externalId'], 0, strlen("V99"))) == "v99" && $account==1 || strtolower(substr($sb_value['externalId'], 0, strlen("A88"))) == "a88" && $account==1 )#舊線的v99不是我們的
                    {
                        continue;
                    }
                    if(strtolower(substr($sb_value['externalId'], 0, strlen($p_value))) == $p_value){
                        if(empty($total[$p_key]['payout'])) $total[$p_key]['payout'] = 0;
                        $total[$p_key]['prefix'] = $p_value;
                        $total[$p_key]['payout'] += $sb_value['customerTotalWL'];
                    }
                    if($p_value == 'hg8'){
                        if(strtolower(substr($sb_value['externalId'], 0, 4)) == 'ehg8'){
                            if(empty($total[$p_key]['payout'])) $total[$p_key]['payout'] = 0;
                            $total[$p_key]['prefix'] = $p_value;
                            $total[$p_key]['payout'] += $sb_value['customerTotalWL'];
                        }
                    }
                    if ($account == 3){
                        if(strtolower(substr($sb_value['externalId'], 6, strlen($p_value))) == $p_value){
                            if(empty($total[$p_key]['payout'])) $total[$p_key]['payout'] = 0;
                            $total[$p_key]['prefix'] = $p_value;
                            $total[$p_key]['payout'] += $sb_value['customerTotalWL'];
                        }
                    }
                    foreach ($spec_code as $spec_key => $spec_v) {
                        if(($sb_value['externalId'] == $spec_v) && ($p_value == 'bet')){
                            if(empty($total[$p_key]['payout'])) $total[$p_key]['payout'] = 0;
                            $total[$p_key]['prefix'] = 'bet';
                            $total[$p_key]['payout'] += $sb_value['customerTotalWL'];
                        }
                    }
                }
            }
        }#回圈结束

        foreach($total as $b){

            $na[]=$b['prefix'];

        }
        array_multisort($na, SORT_ASC, $total);
        return $total;

    }


    public function index_detail($s_date,$e_date,$game=null,$plat){

        $n = 0;
        $total = [];
        for($account=1 ;$account<=2;$account++){

            $st = date("m/d/Y", strtotime($s_date));
            $en = date("m/d/Y", strtotime($e_date));

            //$prefix = $this->prefixlist();

            $jar=new CookieJar;

            //取得登入頁
            switch ($account){
                case 1:
                    $line='98ywj6n';
                    $post = array(
                        'UserName'=>'bgecs',
                        'Password'=>'c4b7W1omfm',
                    );
                    break;
                case 2:
                    $line='ad3b7bc';
                    $post = array(
                        'UserName'=>'BOSCS',
                        'Password'=>'tntC3HsfzdGi',
                    );
                    break;
                case 3:
                    $line='cjbgwue';
                    $post = array(
                        'UserName'=>'bscs',
                        'Password'=>'fmGHc7wJG2sL',
                    );
                    break;
            }
            $login_url = 'http://portal8.bpadmin999.com/' . $line . '/Auth/Login?Language=cs';
            $header = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            ];


            $login = $this->bodytool($login_url, $header, 'POST', $jar, $post);

            //取得token值
            $token_url = 'http://portal8.bpadmin999.com/' . $line . '/Verify/ApplyToken?ActionLink=CustomerWinlossReport&Type=1';
            $tmp_token = $this->bodytool($token_url, $header, 'GET', $jar, null);
            $dom = new DOMDocument();
            @$dom->loadHTML($tmp_token);
            $xpath = new DOMXpath($dom);
            $query = $xpath->query("//input[@name='__RequestVerificationToken']");
            foreach ($query as $key => $value) {
                $token[$key] = $value->getAttribute('value');
            }

            //取得比數
            $num_url = 'http://lap.amazzu.com/CustomerWinLossReport/Index';
            $post = array(
                '__RequestVerificationToken'=>$token[1],
                'DateRange'=>$s_date.'-'.$e_date,
                'FromDate'=>$s_date,
                'ToDate'=>$e_date,
                'CurrencyName'=>'CNY',
                'CurrencyId'=>'13',
                'BaseCurrency'=>'',
                'Username'=>'',
                'SB'=>'SB',
                'NG'=>'NG',
                'BA'=>'BA',
                'VS'=>'VS',
                'KN'=>'KN',
                'VS2'=>'VS2',
                'ProductOptions[0].Code'=>'SB',
                'ProductOptions[0].Name'=>'体育博彩',
                'ProductOptions[0].Status'=>'true',
                'ProductOptions[1].Code'=>'NG',
                'ProductOptions[1].Name'=>'百练赛',
                'ProductOptions[1].Status'=>'true',
                'ProductOptions[2].Code'=>'BA',
                'ProductOptions[2].Name'=>'BA',
                'ProductOptions[2].Status'=>'true',
                'ProductOptions[3].Code'=>'VS',
                'ProductOptions[3].Name'=>'虚拟运动',
                'ProductOptions[3].Status'=>'true',
                'ProductOptions[4].Code'=>'KN',
                'ProductOptions[4].Name'=>'快乐彩',
                'ProductOptions[4].Status'=>'true',
                'ProductOptions[5].Code'=>'VS2',
                'ProductOptions[5].Name'=>'虚拟运动2',
                'ProductOptions[5].Status'=>'true',
                'ProductOptions[6].Code'=>'CC',
                'ProductOptions[6].Name'=>'乐卡迪亚',
                'ProductOptions[6].Status'=>'true',
                'IsExcludedTestData'=>'true',
                'PageIndex'=>'1',
                'PageSize'=>'500',
                'OrderItem'=>'2',
                'IsAscSort'=>'true',
            );
            $num = $this->bodytool($num_url, $header, 'POST', $jar, $post);
            $num = json_decode($num,true);
            $data_url = 'http://lap.amazzu.com/CustomerWinLossReport/Index';
            $post = array(
                '__RequestVerificationToken'=>$token[1],
                'DateRange'=>$s_date.'-'.$e_date,
                'FromDate'=>$s_date,
                'ToDate'=>$e_date,
                'CurrencyName'=>'CNY',
                'CurrencyId'=>'13',
                'BaseCurrency'=>'',
                'Username'=>'',
                'SB'=>'SB',
                'NG'=>'NG',
                'BA'=>'BA',
                'VS'=>'VS',
                'KN'=>'KN',
                'VS2'=>'VS2',
                'ProductOptions[0].Code'=>'SB',
                'ProductOptions[0].Name'=>'体育博彩',
                'ProductOptions[0].Status'=>'true',
                'ProductOptions[1].Code'=>'NG',
                'ProductOptions[1].Name'=>'百练赛',
                'ProductOptions[1].Status'=>'true',
                'ProductOptions[2].Code'=>'BA',
                'ProductOptions[2].Name'=>'BA',
                'ProductOptions[2].Status'=>'true',
                'ProductOptions[3].Code'=>'VS',
                'ProductOptions[3].Name'=>'虚拟运动',
                'ProductOptions[3].Status'=>'true',
                'ProductOptions[4].Code'=>'KN',
                'ProductOptions[4].Name'=>'快乐彩',
                'ProductOptions[4].Status'=>'true',
                'ProductOptions[5].Code'=>'VS2',
                'ProductOptions[5].Name'=>'虚拟运动2',
                'ProductOptions[5].Status'=>'true',
                'ProductOptions[6].Code'=>'CC',
                'ProductOptions[6].Name'=>'乐卡迪亚',
                'ProductOptions[6].Status'=>'true',
                'IsExcludedTestData'=>'true',
                'PageIndex'=>'1',
                'PageSize'=>$num['totalRows'],
                'OrderItem'=>'2',
                'IsAscSort'=>'true',
            );
            $data = $this->bodytool($data_url, $header, 'POST', $jar, $post);
            $data = json_decode($data,true);


            $spec_code = array('a852000a','hyeongoon','zhxhruc','q79548088','lpk0609','ssyuanyu','zyanhao','chenlei30','gang3143','zhangxueyou','test987');


            foreach ($data['dataList'] as $sb_key => $sb_value) {
                if(strtolower(substr($sb_value['externalId'], 0, strlen("BET88"))) == "bet88")
                {
                    continue;
                }
                if(strtolower(substr($sb_value['externalId'], 0, strlen($plat))) == $plat){

                    if($account == 1){
                        $memberCode = strtolower($sb_value['externalId']);
                        //echo $memberCode."  ".$sb_value['customerTotalWL'].PHP_EOL;
                    }
                    else{
                        $memberCode = strtolower(substr($sb_value['externalId'],strlen($plat)+1));
                    }
                    if(isset($total)){
                        $tmp_member = array_column($total, 'memberCode');

                    }
                    if(isset($tmp_member)){
                        if(!in_array($memberCode, $tmp_member)){
                            $total[$n]['memberCode'] = $memberCode;
                            $total[$n]['payout'] = $sb_value['customerTotalWL'];
                            $n++;
                        }
                        else{

                            $found_key = array_search($memberCode, $tmp_member);
                            $total[$found_key]['payout'] += $sb_value['customerTotalWL'];
                        }
                    }
                    else{
                        $total[$n]['memberCode'] = $memberCode;
                        $total[$n]['payout'] = $sb_value['customerTotalWL'];
                        $n++;
                    }



                }
                if($plat == 'hg8'){

                    if(strtolower(substr($sb_value['externalId'], 0, 4)) == 'ehg8'){
                        if($account == 1){
                            $memberCode = strtolower($sb_value['externalId']);
                        }
                        else{
                            $memberCode = strtolower(substr($sb_value['externalId'],5));
                        }

                        if(isset($total)){
                            $tmp_member = array_column($total, 'memberCode');

                        }
                        if(isset($tmp_member)){
                            if(!in_array($memberCode, $tmp_member)){
                                $total[$n]['memberCode'] = $memberCode;
                                $total[$n]['payout'] = $sb_value['customerTotalWL'];
                                $n++;
                            }
                            else{

                                $found_key = array_search($memberCode, $tmp_member);
                                $total[$found_key]['payout'] += $sb_value['customerTotalWL'];
                            }
                        }
                        else{
                            $total[$n]['memberCode'] = $memberCode;
                            $total[$n]['payout'] = $sb_value['customerTotalWL'];
                            $n++;
                        }

                    }
                }
                foreach ($spec_code as $spec_key => $spec_v) {
                    if(($sb_value['externalId'] == $spec_v) && ($p_value == 'bet')){
                        $memberCode = 'bet'.$sb_value['externalId'];
                        if(isset($total)){
                            $tmp_member = array_column($total, 'memberCode');

                        }
                        if(isset($tmp_member)){
                            if(!in_array($memberCode, $tmp_member)){
                                $total[$n]['memberCode'] = $memberCode;
                                $total[$n]['payout'] = $sb_value['customerTotalWL'];
                                $n++;
                            }
                            else{

                                $found_key = array_search($memberCode, $tmp_member);
                                $total[$found_key]['payout'] += $sb_value['customerTotalWL'];
                            }
                        }
                        else{
                            $total[$n]['memberCode'] = $memberCode;
                            $total[$n]['payout'] = $sb_value['customerTotalWL'];
                            $n++;
                        }

                    }
                }
            }


        }#回圈结束

        return $total;

    }

    public function bodytool($url,$header,$method,$cookie,$post=null,$type='form_params',$query=null){								//抓取body

        if($cookie!=null){
            $client=new Client(array(
                'cookies' => $cookie,
                'verify' => false,
            ));
        }else{

            $client=new Client(array(
                'verify'=> false,
            ));

        }

        if($query!=null){

            $res =$client->request($method,$url,[

                'headers' => $header,
                "$type" => $post,
                'query' => $query,

            ]);

        }else if($post!=null){

            $res =$client->request($method,$url,[
                'headers' => $header,
                "$type"=>$post,
            ]);

        }else{

            $res =$client->request($method,$url,[
                'headers' => $header,
            ]);

        }

        $body =$res->getbody()->getcontents();
        //echo $res->getStatusCode();
        return $body;

    }
}

// $x=new sb;
// $x->index('2019-08-26','2019-08-27');