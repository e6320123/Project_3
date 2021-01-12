<?php 
// require_once('../../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class pt extends tools{

	public function index($s_date,$e_date,$game=null){
		$e_date=new DateTime($e_date);
		$e_date=$e_date->modify('+1 day')->format("Y-m-d");
		$loginurl='https://kiosk.betlink.info/api/admin/login';
		$header=array(

			'Content-Type'=>'application/x-www-form-urlencoded; charset=UTF-8',

		);
		$post=array(

			'username'=>'GTLCCNYDFBOS01',
			'password'=>'5IYvgk7O8pc4',

		);

		$keydata=$this->bodytool($loginurl,$header,'POST',null,$post);
		$keydata=json_decode($keydata,true);
		$key=$keydata['result']['secret'];
		$reurl='https://kiosk.betlink.info/api/customreport/getdata';
		$header2=array(

			//'Accept'=>'*/*',
			// 'Accept-Encoding'=>'gzip, deflate, br',
			// 'Accept-Language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			//'Content-Type'=>'application/x-www-form-urlencoded; charset=UTF-8',
			'Cookie'=>'kioskadmin_secret='.$key,
			'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
			'X-Requested-With'=>'XMLHttpRequest',

		);
		$post2=array(

			'reportname'=>'GameStats',
			'perPage'=>'200',
			'page'=>'1',
			'timeperiod'=>'specify',
			'startdate'=> $s_date.' 00:00:00',
			'enddate'=> $e_date.' 00:00:00',
			'gametype'=>'both',
			'livenetwork'=>'0',
			'showjackpot'=>'1',
			'reportby'=>'kioskname',
			'sortby'=>'games',
			'currency'=>'native',

		);
		$data=$this->bodytool($reurl,$header2,'POST',null,$post2);
		$data=json_decode($data,true);
		foreach($data['result'] as $k => $v){

			preg_match('/GTLCCNYDFBOS(.+)/',$v['KIOSKNAME'],$prefix);
			$payout=$v['INCOME']+$v['JACKPOTBETS#']-$v['JACKPOTWINS#'];
			if($payout!=0){

				$total[$k]=[
					'prefix'=>$prefix[1],
					'payout'=>$payout,
				];

			}
			
		}
		return $total;

	}

}

$x=new pt;
$x->index('2019-07-01','2019-07-02');