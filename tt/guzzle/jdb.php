<?php 
// require_once('../../vendor/autoload.php');
error_reporting(0); //不報錯誤
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

class jdb extends tools{

	public function index($s_date,$e_date){

		date_default_timezone_set('Asia/Taipei');
        $st = date("Y-m-d", strtotime($s_date));
        $en = date("Y-m-d", strtotime($e_date));
        $st = "$st 00:00:00";
        $en = "$en 23:59:59";

		$jar=new FileCookieJar('cookie.txt',TRUE);

		$prefix = $this->prefixlist();
        foreach ($prefix as $key => $value) {
        	if(strlen($value) == 4){
        		$four_arr[] = $value;
        	}
        }

		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];


		//取得登入頁
		$login_url = 'https://ag12.jdb1688.net/agentAPI.do';		
		$post = array(
			'action' => '101',
            'dc' => 'BG',
            'id' => 'subbgmacs',
            'password' => '625eb059b6e373d419ad6b1a6f88e0e50250b1f9',
		);
		$login = $this->bodytool($login_url, $header, 'POST', $jar, $post);
		
		
		//取得輸贏
		$data_url = 'https://agapi.jdb199.com/api/runDao';
		$json = array(
			'dao'=> 'GetWinLossReport',
			'domain'=> 'BG',
			'fromDate'=> strtotime($st).'000',
			'fromHour'=> '-1',
			'gametype'=> '0,18,7,9,12',
			'lvl'=> '22',
			'targetId'=> 'subbgmacs@bg',
			'toDate'=> strtotime($en).'999',
			'userid'=> 'subbgmacs'
		);
		$data = $this->bodytool($data_url, $header, 'POST', $jar, $json, 'json');
		$data = json_decode($data,true);
		$n = 0;
		foreach($data['data']['GetWinLossReport'] as $tmp_key => $tmp_val){
			if(in_array(substr($tmp_val['UID_A'],2,4),$four_arr)){
				$total[$n]['prefix'] = substr($tmp_val['UID_A'],2,4);
				$total[$n]['payout'] = $tmp_val['MEMBER_WINLOSS'];
				$n++;
			}
			else{
				$total[$n]['prefix'] = substr($tmp_val['UID_A'],2,3);
				$total[$n]['payout'] = $tmp_val['MEMBER_WINLOSS'];
				$n++;
			}
			
		}

		if($data['data']['totalPages'] > 1){
			for($i = 2; $i <= $data['data']['totalPages']; $i++){
				$data_url = 'https://agapi.jdb199.com/api/runDao';
				$json = array(
					'dao'=> 'GetWinLossReport',
					'domain'=> 'BG',
					'fromDate'=> strtotime($st).'000',
					'fromHour'=> '-1',
					'gametype'=> '0,18,7,9,12',
					'lvl'=> '22',
					'pageNum' => $i,
					'pageRows' => '50',
					'targetId'=> 'subbgmacs@bg',
					'toDate'=> strtotime($en).'999',
					'userid'=> 'subbgmacs'
				);
				$data = $this->bodytool($data_url, $header, 'POST', $jar, $json, 'json');
				$data = json_decode($data,true);

				foreach($data['data']['GetWinLossReport'] as $tmp_key => $tmp_val){
					if(in_array(substr($tmp_val['UID_A'],2,4),$four_arr)){
						$total[$n]['prefix'] = substr($tmp_val['UID_A'],2,4);
						$total[$n]['payout'] = $tmp_val['MEMBER_WINLOSS'];
						$n++;
					}
					else{
						$total[$n]['prefix'] = substr($tmp_val['UID_A'],2,3);
						$total[$n]['payout'] = $tmp_val['MEMBER_WINLOSS'];
						$n++;
					}
				}
			}
		}

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		//print_r($total);
		return $total;

	}

	
}

// $x=new jdb;
// $x->index('2019-08-01','2019-08-02');