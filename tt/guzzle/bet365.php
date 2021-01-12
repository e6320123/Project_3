<?php 
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';


class bet365 extends tools {

   
    public function index($s_date,$e_date,$game=null){
       
        $jar = new CookieJar;
        $login_url = "https://agent.gotobet365.com/trading-api/trader-api/v1/api/internalLogin";
        $header=[];
        $post=[
            'username'=> 'bosbw',
            'password'=> 'Aaaa1111',
            'application'=> 'sb-backoffice',
        ];
       
        $a=$this->bodytool($login_url,$header,'POST',$jar,$post);
        $a=json_decode($a,1);
        $token=$a['Login']['sessionToken'];
        // $start_dt = strtotime($s_date.' 00:00:00').'000';
        // $end_dt = strtotime($e_date.' 23:59:59').'999';
        // 根據你主機php time zone時間調整至 UTC0
        $start_dt = strtotime('+1 hour',strtotime($s_date.' 00:00:00')).'000';
        $end_dt = strtotime('+1 hour',strtotime($e_date.' 23:59:59')).'999';

        $get_report_url = "https://agent.gotobet365.com/trading-api/backoffice/rest/customerdata/report/agent-master-totals";
        $header2=[
            'Content-Type'=>'application/json',
            'sessiontoken'=>$token,
            'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36',

        ];
        $post2='{"timezone":"America/Puerto_Rico","accountPath":"","fromDate":'."$start_dt".',"toDate":'."$end_dt".'}';
        $res=$this->bodytool($get_report_url,$header2,'POST',$jar,$post2,'body');
        $result = json_decode($res);



        foreach($result->value->rows as $plat => $row){
            //切開數組
            $data = explode(",",$row);
            //prefix
            preg_match('/bos(.+)/', $data[1],$prefix);
            $prefix=$prefix[1];
            //payout
            $payout=$data[5];
            if(isset($total[$prefix])){
                @$total[$prefix]['payout']+=$payout;
            }else{
                $total[$prefix]=[
                    'prefix'=>$prefix,
                    'payout'=>$payout,
                ];
            }

        }
        return $total;

        
    }

}


// $og = new Og_new;
// $og->login();