<?php 
//require_once('../vendor/autoload.php');
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


class cr extends tools{

	public function index($s_date,$e_date,$game=null){


		$jar = new CookieJar;
		$header = array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		);
	
		//登入
		$loging_url = 'https://sag.ibocity9.com/app/control/world/login.php?langx=zh-tw&username=boscs001&passwd=aaa123451&passwd_safe=123qwe';
		$login = $this->bodytool($loging_url,$header,'GET',$jar);
		preg_match("/top.user_id = \'(.*)\'/", $login, $uid);

		//取得報表
		$data_url = 'https://sag.ibocity9.com/app/control/world/report_new/report_suagent.php';
		$query = array(
			'uid'=>$uid[1],
			'report_kind'=>'A',
			'result_type'=>'Y',
			'sid'=>'58',
			'date_start'=>$s_date,
			'date_end'=>$e_date,
		);
		$get_data = $this->bodytool($data_url,$header,'GET',$jar,null,'query',$query);
		$dom = new domDocument();
		@$dom->loadHTML($get_data);
		$tables = $dom->getElementsByTagName('table')->item(3)->getElementsByTagName('tr');
		$plat_n = 0;
		$payout_n = 0;
		$total_n = 0;
		foreach ($tables as $key => $value) {
			//echo $key.' :  '.$value->nodeValue.PHP_EOL;
			$prefix = $value->getElementsByTagName('td')->item(1)->nodeValue;
			$payout = $value->getElementsByTagName('td')->item(8)->nodeValue;
			if(preg_match("/bos/i",$prefix)){
				$total[$key]['prefix'] = substr($prefix, 3);
				$total[$key]['payout'] = $payout;
				if($total[$key]['prefix']=='0789'){
					$total[$key]['prefix'] = '789';
				}
				if($total[$key]['prefix']=='DFA10'){
					$total[$key]['prefix'] = 'dfa';
				}
				if($total[$key]['prefix']=='YH50'){
					$total[$key]['prefix'] = 'yh5';
				}
				if($total[$key]['prefix']=='GBT01'){
					$total[$key]['prefix'] = 'gbt';
				}
				if($total[$key]['prefix']=='BOB10'){
					$total[$key]['prefix'] = 'bob';
				}
				if($total[$key]['prefix']=='66801'){
					$total[$key]['prefix'] = '668';
				}
				if($total[$key]['prefix']=='PJW1'){
					$total[$key]['prefix'] = 'pjw';
				}
				if($total[$key]['prefix']=='ZZZ1'){
					$total[$key]['prefix'] = 'zzz';
				}
				if($total[$key]['prefix']=='336901'){
					$total[$key]['prefix'] = '3369';
				}
				if($total[$key]['prefix']=='GZC1'){
					$total[$key]['prefix'] = 'gzc';
				}
			}
			
		}
		
		return $total;
		
	}
}

// $x=new cr();
// $x->index('2019-10-02','2019-10-02');