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

class xbb extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('Y-m-d', strtotime("-1 day",strtotime($s_date)));
        $e_date = date('Y-m-d', strtotime($e_date));

		//登入頁面
		$jar=new CookieJar;

		$mainurl = 'https://man.xbb-slot.com/';
		$header=array(
			'user-agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
		);
		$main=$this->bodytool($mainurl,$header,'GET',$jar);

		//登入
		$loginurl='https://man.xbb-slot.com/api/auth/login';

		$post=array(

			'user_role'=>4,
			'username'=>'boscs',
			'password'=>'Hf7SgoUXbK2P',
			'partner_domain_code'=>'bb9',
			'is_son'=>true,

		);

		$post_js = json_encode($post);
		$login = $this->bodytool($loginurl,$header,'POST',$jar,$post_js,'body');

		//取得輸贏
		$reporturl = 'https://man.xbb-slot.com/api/report';
		$query_data = array(
			'page'=>'1',
			'per_page'=>'1000',
			'user_role'=>'4',
			'start_date'=>$s_date.'T16:00:00.000Z',
			'end_date'=>$e_date.'T15:59:59.000Z',
			'user_id'=>'29',
			'game_id'=>'0',
			'search_type'=>'1',
			'is_test'=>'N',
			'currency'=>'all',
		);

		$data = $this->bodytool($reporturl,$header,'GET',$jar,null,null,$query_data);
		$data = json_decode($data,true);

		foreach ($data['data']['data'] as $key => $value) {
			$total[$key]['prefix'] = substr($value['username'], 3);
			$total[$key]['payout'] = $value['payout_amount'];
		}

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		// print_r($total);
		return $total;
		
		

	}

}

// $x=new leg;
// $x->index('2019-07-01','2019-07-02');