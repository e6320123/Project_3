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

class gd extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date('Y-m-d', strtotime("+1 day",strtotime($e_date)));


        $jar=new CookieJar;
		//登入
		$loginurl='http://gdbackoffice.fastnoodle88.com/colds/merchantreportsite/Code/login2.php';
		$header=array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		);
		$post=array(
			'merchantCode'=>'BOS',
			'accountID'=>'boscs01',
			'psw'=>md5('iArjyCMk'),
		);
		$login = $this->bodytool($loginurl,$header,'POST',$jar,$post);


		//取得平台
		$plat_url = 'http://gdbackoffice.fastnoodle88.com/colds/merchantreportsite/js/ui.php';
		$plat = $this->bodytool($plat_url,$header,'GET',$jar,null);

		preg_match_all('/(?<=MerchantList = \[)(.*)(?=\])/', $plat, $merchantList);
		preg_match_all('/(?<=\")\w+(?=\")/', $merchantList[0][0], $plat_t);
		// print_r($plat_t);

		//取得輸贏
		$data_url = 'http://gdbackoffice.fastnoodle88.com/colds/merchantreportsite/check_in.php';

		$data_post = array(
			'fromdatepicker'=>$s_date,
			'fromTime'=>'12:00:00',
			'todatepicker'=>$e_date,
			'toTime'=>'11:59:59',
			'datetypeSelect'=>'',
			'currencySelect'=>'CNY',
			'merchantSelect'=>'BOS',
			'gameTypeSelect'=>'',
			'tableIDSelect'=>'',
			'networkSelect'=>'',
			'affiliateSelect'=>'',
			'platformSelect'=>'',
			'speedSearch'=>'0',
			'SearchByBalanceTime'=>'0',
			'page'=>'1',
			'gameInterfaceSelect'=>'',
			'a'=>'PNLReportDetail',
		);
		$data = $this->bodytool($data_url,$header,'POST',$jar,$data_post);

		$tmp_data = json_decode($data,true);
		
		$n = 0;
		foreach ($tmp_data['data'] as $key => $value) {
			$total[$n]['prefix'] = substr($key, 3);
			$total[$n]['payout'] = $value['CNY']['gain_amount'];
			$n++;
		}
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		// print_r($total);
		return $total;

	}

}

// $x=new gd;
// $x->index('2019-07-01','2019-07-31');