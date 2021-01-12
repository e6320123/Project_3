<?php 
//require_once('../vendor/autoload.php');
error_reporting(0); //不報錯誤
date_default_timezone_set("Asia/Taipei");
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

class ab extends tools{

	public $platform = ['ab_live','ab_egame'];
	public $apiurl = 'http://ab.bjzthd.cn';
	public $other_apiurl = 'https://ams.allbetgaming.net';
	public $platform_data;
	public $report_url = ['service/reports/profit/1165/subagents','service/reports/profit/1166/subagents'];
	private $username = ['bgcs2','bgecsxpj'];
	private $password = ['Aa123456@','1G2rq0e#'];
	private $agentId = ['1165','1166'];
	public function index($s_date,$e_date,$game=null){
		
		$s_date = date('Y-m-d H:i:s', strtotime("+12 Hour",strtotime($s_date)));
		$e_date = date('Y-m-d H:i:s', strtotime("+36 Hour",strtotime($e_date)));

		$jar=new CookieJar;

		$platform_num = $game - 1;
		if($game == 1){
			$this->report_url = ['service/reports/profit/1165/subagents','service/reports/profit/1166/subagents'];
		}
		else{
			$this->report_url = ['service/reports/profit/AF/1165/subagents','service/reports/profit/AF/1166/subagents'];
		}
		
		// $header = array(
		// 	'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		// );
		// //取得token
		// $token_url = 'https://ams.allbetgaming.net/service/auth/preset';
		// $token = $this->bodytool($token_url,$header,'GET',$jar);
		// $token = json_decode($token,true);


		// //取得驗證碼
		// $captcha_url = 'data:image/jpeg;base64,'.$token['data']['captcha'];
		// list(,$captcha_url) = explode(',',$captcha_url);
		// $captcha_url = base64_decode($captcha_url);


		// //登入
		// $loginurl='https://ams.allbetgaming.net/service/auth';
		// $post=array(
		// 	'captcha'=>$this->captcha($captcha_url,200),
		// 	'password'=>'Aa123456@',
		// 	'refer'=>'https://ams.allbetgaming.net/',
		// 	'token'=>$token['data']['token'],
		// 	'username'=>'bgcs2',
		// );
		// $login = $this->bodytool($loginurl,$header,'POST',$jar,$post,'json');
		// $login = json_decode($login, true);


		//取得報表
		for ($switch=0; $switch <= 1; $switch++) {
			$header = array(
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
				'Cookie' => '__nxquid=XsZrdQAAAACjm/WrDU5Vkg==62920020; loginErrorMsg=null; loginErrorMsgParams=null; lang=zh_cn; langText=%E7%AE%80%E4%BD%93; firstTimeLogin=false; amsIP=13.78.85.146; amsVersion=9.0; userInfo={%22userId%22:5547%2C%22userN22%2C%22allPermission%22:true%2C%22subAccountName%22:%22bgcs2%22%2C%22agentUserName%22:%22sg5qyy5%22%2C%22creame%22:%22bgcs2%22%2C%22agentId%22:1165%2C%22subAccountId%22:5547%2C%22level%22:4%2C%22role%22:%22Visitor%dit%22:27390706.5%2C%22currencyId%22:1%2C%22currencyUnit%22:%22CNY%22%2C%22aeagent%22:false%2C%22firstTimeLogin%22:false}; token=9438be2d95419dd95fef9a91b8dbc72e; C3VK=9c3652',
			);
			//取得token
			// $token_url = 'https://ab.bjzthd.cn/service/auth/preset';
			// $token = $this->bodytool($token_url,$header,'GET',$jar);
			// $token = json_decode($token,true);


			// //取得驗證碼
			// $captcha_url = 'data:image/jpeg;base64,'.$token['data']['captcha'];
			// list(,$captcha_url) = explode(',',$captcha_url);
			// $captcha_url = base64_decode($captcha_url);


			//登入
			/*
			$loginurl='https://ab.bjzthd.cn/service/auth';
			$post=array(
				'captcha'=>$this->captcha($captcha_url,200),
				'password'=>$this->password[$switch],
				'refer'=>'https://ab.bjzthd.cn/',
				'token'=>$token['data']['token'],
				'username'=>$this->username[$switch],
			);
			$login = $this->bodytool($loginurl,$header,'POST',$jar,$post,'json');
			$login = json_decode($login, true);
			*/

			//取得報表
			$reurl='https://ab.bjzthd.cn/'.$this->report_url[$switch];
			$header = array(
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
				'Cookie' => '__nxquid=XsZrdQAAAACjm/WrDU5Vkg==62920020; loginErrorMsgParams=null; lang=zh_cn; langText=%E7%AE%80%E4%BD%93; firstTimeLogin=false; amsIP=13.78.85.146; amsVersion=10.0; loginErrorMsg=null; token=c3e9e4638ad755c2b414acaf4d0ad06e; userInfo={%22userId%22:5547%2C%22userName%22:%22bgcs2%22%2C%22agentId%22:1165%2C%22subAccountId%22:5547%2C%22level%22:4%2C%22role%22:%22Visitor%22%2C%22allPermission%22:true%2C%22subAccountName%22:%22bgcs2%22%2C%22agentUserName%22:%22sg5qyy5%22%2C%22credit%22:26819195.41%2C%22currencyId%22:1%2C%22currencyUnit%22:%22CNY%22%2C%22aeagent%22:false%2C%22firstTimeLogin%22:false}; C3VK=8cba12
				',
				'x-token' => 'c3e9e4638ad755c2b414acaf4d0ad06e',
			);
			$query=array(
		        'agentId'=>$this->agentId[$switch],
		        'page.numPerPage'=>'100',
		        'page.pageNum'=>'1',
		        'belongTo'=>'subAgent',
		        'startTime'=>strtotime($s_date).'000',
		        'endTime'=>strtotime($e_date).'000',
			);
			$report=$this->bodytool($reurl,$header,'GET',$jar,null,null,$query);
			$report = json_decode($report, true);

			foreach ($report['data']['agentDetails']['groups'] as $key => $value) {
				$prefix = $value['agents']['0']['agent']['userName'];
				if(preg_match('#^.{5}y5#', $prefix)){
					$prefix = $this->prefix_match($prefix);
				}
				$payout = $value['agents']['0']['total']['winOrLossAmount'];
				$this->platform_data[$prefix][$this->platform[$platform_num]] = $payout;
				
			}
		}
	
		switch ($game) {
			case '1':
				foreach ($this->platform_data as $k => $v) {
					$total[]=[
						'prefix'=>$k,
						'payout'=>$v['ab_live'],
					];
				}
				break;

			case '2':
				foreach ($this->platform_data as $k => $v) {
					$total[]=[
						'prefix'=>$k,
						'payout'=>$v['ab_egame'],
					];
				}
				break;

		}
		return $total;

		

	}


	public function prefix_match($prefix){
	    if($prefix == 'bgtbgy5') $prefix = 'txx';
	    $prefix = str_replace("bg", "", $prefix);
	    $prefix = str_replace("y5", "", $prefix);
	    $prefix = str_replace("b_", "", $prefix);
	    $prefix = str_replace("代理报表", "", $prefix);
	    $prefix = str_replace("jsh", "jss", $prefix);
	    $prefix = str_replace("bet88", "t88", $prefix);
	    $prefix = str_replace("bet36", "bet", $prefix);
	    $prefix = str_replace("bcna", "cna", $prefix);
	    $prefix = str_replace("bzwns", "zwns", $prefix);
	    $prefix = str_replace("b1xxp", "xxp", $prefix);
	    $prefix = str_replace("blg", "blgj", $prefix);
	    $prefix = str_replace("bb131", "b131", $prefix);
	    $prefix = str_replace("b3368", "3368", $prefix);
	    $prefix = str_replace("b3668", "3668", $prefix);
	    $prefix = str_replace("baxpj", "axpj", $prefix);
	    $prefix = str_replace("b1xxp", "xxp", $prefix);
	    $prefix = str_replace("bsvip", "svip", $prefix);
	    if($prefix == 'txx') $prefix = 'tbg';
	    return $prefix;
	}
}
// $x=new ab;
// $x->index('2019-09-25','2019-09-25',1);