<?php
// require_once "vendor/autoload.php";
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class fg2 extends tools{

    public $save_arr = [];
    public function index($s_date,$e_date,$game=null){

        $jar=new CookieJar;
        $loginurl='https://ui.adminfg.com/oauth/token';
        $post=[
            'grant_type'=> 'password',
            'username'  => 'FGBOS_CAIWU',
            'password'  => 'pMgLoSCwz299',
        ];
        $res=$this->bodytool($loginurl,null,'POST',$jar,$post);
        $token = json_decode($res);
        $header=[
            'authorization' => 'Bearer ' . $token->access_token,
            'X-DAS-CURRENCY' => 'CNY',
            'X-DAS-LANG' => 'zh-CN',
            'X-DAS-TX-ID' => $token->jti,
            'X-DAS-TZ' => 'UTC-04:00',
        ];
        $page_args = [
            'page'=> '1',
            'page_size'=> '200',
            'recursive'=> 'false',
            'sort_by'=> 'NAME',
            'desc'=> 'false',
            'stats'=> 'true',
        ];
        $url = 'https://api.adminfg.com/v1/account/142498539/children?'.http_build_query($page_args);
        $res =$this->bodytool($url,$header,'get',$jar);
        $res = json_decode($res, true);

        foreach($res['data'] as  $item) {
            $this->save_arr[ $item['id'] ] = strtolower( str_replace('FGBOS', '', $item['name']) );
        }

        $page_args = [
            'start_time'    => $s_date,
            'end_time'      => $e_date,
            'page'          => '1',
            'page_size'     => '200',
            'include_test'  => 'true',
            'dimensions'    => 'downline_id',
            'metrics'       => 'unique_member,nr_tx_sum,nr_tx:wager_sum,sub[amount:wager_sum|amount:refund_sum:credit],sub[amount:payout_sum|amount:refund_sum:debit],gross_revenue_sum,div[gross_revenue_sum|sub[amount:wager_sum|amount:refund_sum:credit]]',
        ];
        $reurl='https://api.adminfg.com/v1/report/142498539?'.http_build_query($page_args);
        $res = $this->bodytool($reurl,$header,'GET',$jar);
        $res = json_decode($res, true);

        $result = [];

        foreach ($res['data']['rows'] as $item ) {
            $business_id = $item[7];
            $winloss = $item[5];
            $result[] = [
                'prefix'=>$this->save_arr[$business_id],
                'payout'=>$winloss,
            ];
            
        }

        return $result;
        
    }
}

//$fg = new FG2();
//print_r($fg->index($argv[1], $argv[2]));

