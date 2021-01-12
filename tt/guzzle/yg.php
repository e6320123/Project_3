<?php
//require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class yg extends tools{

	public function index($s_date,$e_date,$game){
        //$game=1 彩票
        //$game=2 棋牌
        $jar=new CookieJar;
        if($game=='1'){ //彩票
            $vurl='https://bsy005atytech.yicaigame.com/admin/logVerifycode.do?';
            $body=$this->bodytool($vurl,null,'GET',$jar,null);
            file_put_contents('image.gif',$body);
            $loginurl='https://bsy005atytech.yicaigame.com/admin/login.do';
            $header=array(

                'Accept'=>'*/*'

            );
            $post=array(

                'username'=>'boscs',
                'password'=>'LHU08X6p',
                'verifyCode'=>$this->captcha($body,200),

            );
            $this->bodytool($loginurl,$header,'POST',$jar,$post);

            $reurl='https://bsy005atytech.yicaigame.com/admin/accountDailyReport/list.do';
            $header2=array(

                'Accept'=>'application/json, text/javascript, */*; q=0.01',

            );
            $post2=array(

                'sortOrder'=>'asc',
                'pageSize'=>'200',
                'pageNumber'=>'1',
                'startTime'=>$s_date,
                'username'=>'',
                'endTime'=>$e_date,

            );
            $redata=$this->bodytool($reurl,$header2,'POST',$jar,$post2);
            $report=json_decode($redata);
            //print_r($report);
            foreach ($report->rows as $k => $v) {
                $payout=$v->unBetOrderMoney-$v->totalWinOrLose;
                $prefix=$v->username;
                preg_match('/bos(.+)/',$prefix,$prefix);
                if($payout!=0){

                    $total[]=[

                        'prefix'=>$prefix[1],
                        'payout'=>$payout,

                    ];

                }
            }

        }else if($game=='2'){   //棋牌
            $vurl='http://wz-admin.nbdns8.com/Home/SafeCode?0.6666091242314003';
            $body=$this->bodytool($vurl,null,'GET',$jar,null);
            file_put_contents('image.gif',$body);
            $loginurl='http://wz-admin.nbdns8.com/Home/Login';
            $header=array(

                'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',

            );
            $logbody=$this->bodytool($loginurl,$header,'GET',$jar,null);
            $dom=new DOMDocument();
            @$dom->loadHTML($logbody);
            $token=$dom->getElementsByTagName("input")->item(6)->attributes->item(2)->nodeValue;
            //login url
            $reloginurl = "http://wz-admin.nbdns8.com/Home/Login";
            $post=array(
                'ReturnUrl'=> '/',
                'LoginName'=>'bosadmin',
                'Password'=>'lE39JFYMMU21',
                'VerificationCode'=> $this->captcha($body,180),
                '__RequestVerificationToken'=> $token,
            );
            $logbody=$this->bodytool($reloginurl,$header,'POST',$jar,$post);
            $reloginurl1 = "http://wz-admin.nbdns8.com/Agent/AgentSiteSettlementReport";
            $post1 = array(
                '_search' => 'true',
                'nd' => '1575537441863',
                'rows' => '1000',
                'page' => '1',
                'sidx' => '',
                'sord' => 'asc',
                'StartTime' => $s_date,
                'EndTime' => $e_date,
            );
            $logbody=$this->bodytool($reloginurl1,$header,'POST',$jar,$post1);
            $dom=new DOMDocument();
            @$dom->loadHTML($logbody);
            $data = json_decode($logbody,true);
            $rows = $data['rows'];
            foreach ($rows as $k => $v)
            {
                $payout=$v['MoneyTotal']-$v['WinMoneyTotal'];
                preg_match('/bos(.+)/',$v['AgentUserName'],$prefix);
                if ($payout != 0)
                {
                    $total[] = [
                        'prefix'=>$prefix[1],
                        'payout'=>$payout,
                    ];
                }
            }
        }
        if(isset($total)){
            return $total;
        }
    }
}
// $x=new yg;
// $x->index();