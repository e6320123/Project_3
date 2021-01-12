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
use GuzzleHttp\Cookie\FileCookieJar;
require_once 'class.tools.php';

class qt extends tools{
	public $jar;
	public $header;
	public function index($s_date,$e_date){
		
        $s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date('Y-m-d', strtotime("+1 day",strtotime($e_date)));

     
		$this->jar=new FileCookieJar('cookie.txt',TRUE);

		//登入
		$login_url = 'https://bo.qtplatform.com/bo-api/v1/auth/token';
		$this->header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$query = array(
			'grant_type'=>'password',
			'response_type'=>'token',
			'username'=>'manager_bgegroup',
			'password'=>'cb78a5ef',
		);
		$login = $this->bodytool($login_url, $this->header, 'POST', $this->jar, null, null, $query);
		$login = json_decode($login,true);
		$token = $login['access_token'];

		//取得平台
		$plat_url = 'https://bo.qtplatform.com/bo-api/profile/operators';
		$this->header = [
			'Authorization' => 'Bearer '.$token,
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$plat = $this->bodytool($plat_url, $this->header, 'GET', $this->jar, null);
		$plat = json_decode($plat, true);

		//取得輸贏

		$data_url = 'https://bo.qtplatform.com/bo-api/reports/ngr';
		foreach ($plat['items'] as $key => $value) {
			$data_query[$key] = array(
				'from'=>$s_date.'T00:00:00',
				'to'=>$e_date.'T00:00:00',
				'operatorId'=>$value['id'],
				'device'=>'NONE',
				'embed'=>'summaries',
			);
		}

		$data = $this->do_pool($data_url, $this->header,$this->jar,null,null,$data_query);
	
		foreach ($data as $key => $value) {
			$tmp_data = json_decode($value,true);
				
			if(substr($plat['items'][$key]['name'],0,3) == 'BGE'){
				$total[$key]['prefix'] = substr($plat['items'][$key]['name'],3);
				$total[$key]['payout'] = isset($tmp_data['items'][0]['totalPayout']) ? $tmp_data['items'][0]['totalBet']-$tmp_data['items'][0]['totalPayout'] : 0;
				
			}
			else{

				$total[$key]['prefix'] = $plat['items'][$key]['name'];
				$total[$key]['payout'] = isset($tmp_data['items'][0]['totalPayout']) ? $tmp_data['items'][0]['totalBet']-$tmp_data['items'][0]['totalPayout'] : 0;
			
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

// $x=new qt;
// $x->index('2019-08-01','2019-08-01');