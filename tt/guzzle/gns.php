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
require_once 'class.tools.php';

class gns extends tools{

	public function index($s_date,$e_date,$game=null){

		$e_date=new DateTime($e_date);
		$e_date=$e_date->modify('+1 day')->format("Y-m-d");
		$jar=new CookieJar;
		$loginurl='https://krugbo-gw.star0ad.com/bo-api/login';
		$header=array(
			'content-type'=> 'application/json',
            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36'
		);
		$post=array(
			'password'=> '125c88528540028630ed59fb94ee85995c4a49ef',
			'userName' => 'BOSCS',
		);
		$b=$this->bodytool($loginurl,$header,'POST',$jar,$post,'json');
		$b = json_decode($b,1);
		$token=$b['accessToken'];


		$reurl='https://krugbo-gw.star0ad.com/bo-api/reports/game-performance/';
		$header2=array(
			'Authorization'=> 'Bearer '.$token,
			'content-type'=> 'application/json',
			'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36'
		);
		$query=array(
			'reportCurrency' => 'CNY',
			'partnerUids' => '17f1deca-da6a-fccd-d79b-33e55d07b9c8',
			'language' => 'zh-TW',
			'view' => 'MERCHANT',
			'startDate' => $s_date.'T04:00:00Z',
			'endDate' => $e_date.'T03:59:59.999Z',
            'freeRoundView' => 'INCLUDE'
		);
		$report=$this->bodytool($reurl,$header2,'GET',$jar,null,null,$query);
		$report=json_decode($report,true);
		foreach ($report['data']['summary'] as $k => $v) {
			$total[$k]=[
				'prefix'=>$k,
				'payout'=>$v['reportCurrencyWinLoss'],
			];
		}
		//print_r($total);
		return $total;
	}
}
// $x=new gns;
// $x->index('2019-08-01','2019-08-02');
