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

class bg extends tools{

	public function index($s_date,$e_date,$game=null){



		switch ($game) {
			case '1':
				$gamecode=[101];
				break;
			case '2':
				$gamecode=[351,456];
				break;
			case '3':
				$gamecode=[105,411,484];
				break;
		}
		$jar=new CookieJar;
		$loginurl='https://owner1-aka.ravown.com/auth/api/auth.sn.login?_t=1561091233459';
		$header=array(
			'Accept'=>'application/json, text/plain, */*',
		);
		$post=array(
			'id' => '1588919852672',
			'params' => [
				'host' => 'vr3765.bgvip88.com',
				'loginId' => 'BOSSupport',
				'saltedPassword' => 'p1xJ2ZPKm6RwDKIT5Qa1rckufkk=',
				'salt' => '1588919852671',
				'captchaKey' => 'ffkk',
				'captchaCode' => '3344',
				'randomStr' => '1588919842000466030767020677.56',
				'checkQrCode' => '1',
				'otp' => 'null',
			],
			'jsonrpc' => '2.0',
			'method' => 'auth.sn.login',
		);
		$body=$this->bodytool($loginurl,$header,'POST',$jar,$post,'json');
		$sessionId=json_decode($body,true)['result']['sessionId'];


		$reurl='https://owner1-aka.ravown.com/zbsngw/api/sn.agent.game.summary.report?_t=1561092055646';
		$post2=array(
			'id' => '1561092055638',
	    	'params' => [
	    		'opsId' => $sessionId,
	    		'terminal' => '1',
	    		'host' => 'vr3765.bgvip88.com',
	    		'beginTime' => $s_date,
	    		'endTime' => $e_date,
	    		'gameIds' => $gamecode,
	    		'page' => '1',
	    		'pageSize' => '9999',
	    	],
	    	'jsonrpc' => '2.0',
	    	'method' => 'sn.agent.game.summary.report',
		);
		$report=json_decode($this->bodytool($reurl,$header,'POST',$jar,$post2,'json'),true);

		foreach ($gamecode as $k => $v) {
			if(isset($report['result']['items'][$v])){
				foreach ($report['result']['items'][$v] as $key => $value) {
					$prefix = substr($value['agent_login_id'], 3);
					$payout = $value['wins'] * -1 + $value['jackpot'];
					if(!isset($total[$prefix])){
						$total[$prefix]=[
							'prefix'=>$prefix,
							'payout'=>$payout,
						];
					}else{
						$total[$prefix]['payout']+=$payout;
					}
				}
			}
		}
		//print_r($total);
		
		return $total;
	}
}
// $x=new bg;
// $x->index('2019-08-01','2019-08-02');