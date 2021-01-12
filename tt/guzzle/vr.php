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

class vr extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('Y/m/d', strtotime($s_date));
        $e_date = date('Y/m/d', strtotime($e_date));


		$jar=new CookieJar;

		//取得登入頁
		$login_pageurl = 'https://vrbetbiz.com/Account/Login';
		$header = array(

			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',

		);
		$login_token = $this->bodytool($login_pageurl, $header, 'GET', $jar, null);
		$dom = new DOMDocument();
		@$dom->loadHTML($login_token);
		$xpath = new DOMXpath($dom);
		$query = $xpath->query("//input[@name='__RequestVerificationToken']");
		foreach ($query as $key => $value) {
			$token = $value->getAttribute('value');
		}

		//登入
		$loginurl = 'https://vrbetbiz.com/Account/Login';
		$post = array(
			'__RequestVerificationToken'=>$token,
			'MerchantCode'=>'BOS',
			'LoginID'=>'Service',
			'Password'=>'a123456',
		);
		$login = $this->bodytool($loginurl, $header, 'POST', $jar, $post);
		//結算查詢
		$merchant_url = 'http://vrbetbiz.com/MerchantSettlement';
		$merchant_token = $this->bodytool($merchant_url, $header, 'GET', $jar, null);
		$dom = new DOMDocument();
		@$dom->loadHTML($merchant_token);
		$xpath = new DOMXpath($dom);
		$query = $xpath->query("//input[@name='__RequestVerificationToken']");
		foreach ($query as $key => $value) {
			$token = $value->getAttribute('value');
		}


		//查詢
		$search_url = 'https://vrbetbiz.com/MerchantSettlement/MerchantSettlementSearch';
		$post = array(
			'__RequestVerificationToken'=>$token,
			'StartTime'=>$s_date,
			'EndTime'=>$e_date,
			'FinancialDayDelimitation'=>'0',
		);
		$search = $this->bodytool($search_url, $header, 'POST', $jar, $post);
		//各家查詢
		$detail_url = 'https://vrbetbiz.com/MerchantSettlement/MerchantSettlementSearch';
		$post = array(
			'__RequestVerificationToken'=>$token,
			'MerchantCode'=>'BOS',
			'StartTime'=>$s_date,
			'EndTime'=>$e_date,
			'FinancialDayDelimitation'=>'0',
		);
		$detail = $this->bodytool($detail_url, $header, 'POST', $jar, $post);
		$tmp_data = json_decode($detail,true);
		// print_r($tmp_data);
		foreach ($tmp_data['Data'] as $key => $value) {

			if(substr($value['MerchantCode'], 0, 3) == 'BOS'){
				if(substr($value['MerchantCode'],3) == 'JSH'){
					$total[$key]['prefix'] = 'jss';
					$total[$key]['payout'] = $value['TotalEarn'];
				}
				else if(substr($value['MerchantCode'],3) == 'B13'){
					$total[$key]['prefix'] = 'b131';
					$total[$key]['payout'] = $value['TotalEarn'];
				}
				else{
					$total[$key]['prefix'] = trim(substr($value['MerchantCode'],3));
					$total[$key]['payout'] = $value['TotalEarn'];
				}
				

			}
			if(substr($value['MerchantCode'], 0, 2) == 'BG'){
				$total[$key]['prefix'] = trim(substr($value['MerchantCode'],2));
				$total[$key]['payout'] = $value['TotalEarn'];
			}
			// else{
			// 	$total[$key]['prefix'] = $value['MerchantName'];
			// 	$total[$key]['payout'] = $value['BetEarnAcc']+$value['OfficialBetEarnAcc']+$value['MarkSixBetEarnAcc']+$value['BaccaratBetEarnAcc']+$value['GGLBetEarnAcc']+$value['DonateEarnAcc'];
			// }
		}
		
		
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		//print_r($total);
		return $total;

	}

}

// $x=new vr;
// $x->index('2019-07-01','2019-07-02');