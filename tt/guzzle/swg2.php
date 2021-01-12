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
use GuzzleHttp\Cookie\FileCookieJar;
require_once 'class.tools.php';

class swg2 extends tools{

	public function index($s_date,$e_date){


        $s_date = date('Y-m-d', strtotime($s_date));
		$e_date = date('Y-m-d', strtotime($e_date));

		$jar=new CookieJar;

		//取得登入頁
		$login_url = 'https://sys.redlabel8.com/api/auth/login';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$post = array(
			'username'=> 'BOSCS',
			'password'=> 'tEAQHPQzi1gp',
		);
		$login = $this->bodytool($login_url, $header, 'POST', $jar, $post);
		$token = json_decode($login,true);

		//取得輸贏
		$data_url = 'https://sys.redlabel8.com/api/reports/game/stat?token='.$token['data']['access_token'];
		$json = array(
			'ReportBy'=>'brand',
			'brand'=>'',
			'end'=>$e_date.' 23:59:59',
			'from'=>$s_date.' 00:00:00',
			'game_type'=>'',
			'player'=>'',
			'provider'=>'',
		);
		$data = $this->bodytool($data_url, $header, 'POST', $jar, $json, 'json');
		$data = json_decode($data,true);
		$n = 0;
		foreach ($data['data']['SW_PLUS'] as $key => $value) {
			$total[$n]['prefix'] = substr($key,3);
		    $total[$n]['payout'] = $value['real_money_income']+$value['jackpot_bet']-$value['jackpot_win'];
		    $n++;
		}

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		//print_r($total);
		return $total;

	}

	
}

// $x=new swg2;
// $x->index('2019-07-01','2019-07-02');