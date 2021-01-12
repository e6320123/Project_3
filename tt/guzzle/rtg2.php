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

class rtg2 extends tools{

	public function index($s_date,$e_date,$game=null){


		$s_date = date('Y-m-d', strtotime("-1 day",strtotime($s_date)));
        $e_date = date('Y-m-d', strtotime($e_date));

        
        // print_r($prefix);
		$jar = new CookieJar;


		//登入
		$loginurl = 'https://is.rtgintegrations.com/connect/token';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$post = array(

			'password'=>'zgZy5eU3',
            'grant_type'=>'password',
            'username'=>'boscs',
            'client_id'=>'ui',
            'client_secret'=>'secret',

		);

		$login = $this->bodytool($loginurl, $header, 'POST', $jar, $post);
		$token = json_decode($login, true);

		//取得平台
		$plat_url = 'https://webapi.rtgintegrations.com/api/agents?agentId=a19f3ef1-9930-4a5d-8a67-3e59f35d17eb&includeSubAgents=false';
		$header = [
			'Authorization' => $token['token_type'].' '.$token['access_token'],
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$plat = $this->bodytool($plat_url, $header, 'GET', $jar, null);
		$plat = json_decode($plat, true);


		//取得輸贏
		$data_url = 'https://cms.rtgintegrations.com/api/report/casinoperformance';
		$post = array(
			'filters' => [],
			'language' => 'en',
			'orderBy' => 'startDate',
			'pageIndex' => '0',
			'pageSize' => count($plat),
			'params' => [
			    'agentId' => 'a19f3ef1-9930-4a5d-8a67-3e59f35d17eb',
			    'aggregation' => 'MONTH',
			    'breakBy' => [
			        'isEnabled' => false,
			        'option' => 'SidebetFeatureType',
			    ],
			    'fromDate' => $s_date.'T16:00:00.000Z',
			    'includeSubagents' => false,
			    'toDate' => $e_date.'T15:59:59.999Z',
			    'utcOffsetInMinutes' => '480',
			   
			],
			'sortDirection' => 'desc',
		);
		$data = $this->bodytool($data_url, $header, 'POST', $jar, $post, 'json');
		$data = json_decode($data,true);

		foreach ($data['items'] as $key => $value) {
			$total[$value['agentName']]['prefix'] = $value['agentName'];
		    $total[$value['agentName']]['payout'] += $value['netWin'];
		}


		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		//print_r($total);
		return $total;

	}


}

// $x=new rtg2();
// $x->index('2019-07-01','2019-07-31');