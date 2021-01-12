<?php 
//require_once('../../vendor/autoload.php');
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

class apl extends tools{

	public function index($s_date,$e_date,$game=null){
		
		$s_date = date('Y/m/d', strtotime($s_date));
        $e_date = date('Y/m/d', strtotime("+1 day",strtotime($e_date)));

        //獲取站台列表
        $prefix = $this->prefixlist();

		$jar = new FileCookieJar('cookie.txt',TRUE);


		//登入
		$loginurl = 'http://tgpgateway.com/api/licensees/auth';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$post = array(

			'licenseeCode' => 'bge',
			'loginUrl' => '~/#!/login/licensee',
			'password' => '123Qwevfr4#',
			'username' => 'bge001',

		);

		$login = $this->bodytool($loginurl, $header, 'POST', $jar, $post);
		$token = json_decode($login, true);

		//取得平台
		$plat_url = 'http://tgpgateway.com/api/brands/codes?sortBy=name';
		$header = [
			'x-anticsrf-token' => $token['csrfToken'],
		];
		$post = array(

			'brandStatus'=> null,
            'canShowArchivedBrand'=> 'false',
            'licenseeCodes'=> [],
            'orderBy'=> 'Ascending',
            'resellerCodes'=> [],
            'restrictViewHasSingleWallet'=> 'false',
            'showAll'=> 'true',

		);

		$tmp_plat = $this->bodytool($plat_url, $header, 'POST', $jar, $post);
		$tmp_plat = json_decode($tmp_plat,true);
		foreach ($prefix as $key => $value) {
		    foreach ($tmp_plat['items'] as $tmp_key => $tmp_value) {
		    	if($value == substr(substr($tmp_value['code'],0,2+strlen($value)),2)){
		    		$plat_code .= $tmp_value['code'].',';
		            $plat_code_n += 1;
		    	}
		        if($value == substr(substr($tmp_value['code'],0,3+strlen($value)),3)){
		            $plat_code .= $tmp_value['code'].',';
		            $plat_code_n += 1;
		        }
		        
		    }
		}
	
		//取得遊戲資料
		$game_url = 'http://tgpgateway.com/api/gameProviders/codes';
		$game = $this->bodytool($game_url, $header, 'GET', $jar, null);
		$game = json_decode($game, true);

		//取得輸贏
		$laxino_url = 'http://tgpgateway.com/api/reports/overall';
		$post = [
			'beginDate'=> "$s_date 00:00",
			'brandCodes'=> [$plat_code],
			'brandStatus'=> null,
			'currencyCodes'=> ['RMB'],
			'endDate'=> "$e_date 00:00",
			'gameProviderIds'=> [],
			'gameType'=> null,
			'includeJackpotContribution'=> 'false',
			'orderBy'=> 'ascending',
			'page'=> 1,
			'platformType'=> '',
			'rows'=> $plat_code_n,
			'sortBy'=> 'updatedOn',
			'testPlayerFilterType'=> '1',
			'timestampType'=> '0',
			'timezone'=> '+0800',
		];
		$laxino = $this->bodytool($laxino_url, $header, 'POST', $jar, $post);
		$laxino = json_decode($laxino, true);
		$p_n = 0;
		foreach ($laxino['items'] as $key => $value) {
			
			if(str_replace(substr($value['brandCode'], -3), '', substr($value['brandCode'], 3)) == 'n888'){
				$prefix = 'n88';
			}
			else if(str_replace(substr($value['brandCode'], -3), '', substr($value['brandCode'], 2)) == 'b68'){
				$prefix = 'b68';
				
			}
			else{
				$prefix = str_replace(substr($value['brandCode'], -3), '', substr($value['brandCode'], 3));
			}
			
			$total[$p_n]['prefix'] = $prefix;
			$total[$p_n]['payout'] = $value['hold'];
			$p_n += 1;
		}
		

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
	
		//print_r($total);
		return $total;

	}


}

// $x=new apl();
// $x->index('2019-07-01','2019-07-02');