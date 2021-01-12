
<?php
// require_once '../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class bti extends tools{

	public function index($s_date,$e_date,$game=null){

		//login page
		$jar=new CookieJar;
		$loginurl='https://sso.bti360.io/Home?returnUrl=%2Fconnect%2Fauthorize%2Fcallback%3Fclient_id%3DPROD_bosadmin.bti360.io%26response_type%3Dcode%2520id_token%26scope%3Dopenid%2520profile%2520offline_access%2520chameleonid%26redirect_uri%3Dhttps%253A%252F%252Fbosadmin.bti360.io%252Flogincallback.ashx%26state%3D7343030d5defd100b6e70f8041ef224974b1050aa01be41def949f4edb0b8175;%252F%26nonce%3D6df056e532d95086963ea7744fc2d446103c47d383b3383e8ec68c3430027c5b%26response_mode%3Dform_post';
		$header=array(

			'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
			'requesttarget' => 'AJAXService',

		);
		$logbody=$this->bodytool($loginurl,$header,'GET',$jar,null);

		$dom=new DOMDocument();
        @$dom->loadHTML($logbody);
        $returnurl=$dom->getElementById("login-wrap")->getElementsByTagName("input")->item(0)->attributes->item(3)->nodeValue;

        //login url
        $reloginurl = "https://sso.bti360.io/Home/";

		$post=array(

			'ReturnUrl'=> $returnurl,
		    'CorporateGroup'=>'bosadmin',
		    'Username'=>'boscs',
		    'Password'=>'ja6Xzmd9u4RmXtnOScM4AFXexaEFz8po1',
		    'ddlLanguage'=>'en-US',

		);
		$logbody=$this->bodytool($reloginurl,$header,'POST',$jar,$post);

		$dom=new DOMDocument();
		@$dom->loadHTML($logbody);
		$code = $dom->getElementsByTagName("input")->item(0)->attributes->item(2)->nodeValue;
		$id_token = $dom->getElementsByTagName("input")->item(1)->attributes->item(2)->nodeValue;
		$scope = $dom->getElementsByTagName("input")->item(2)->attributes->item(2)->nodeValue;
		$state = $dom->getElementsByTagName("input")->item(3)->attributes->item(2)->nodeValue;
		$session_state = $dom->getElementsByTagName("input")->item(4)->attributes->item(2)->nodeValue;

		$reloginurl = "https://sso.bti360.io/connect/authorize/callback";

		$query = array(
			'client_id' => 'PROD_bosadmin.bti360.io',
			'response_type' => 'code id_token',
			'scope' => 'openid profile offline_access chameleonid',
			'redirect_uri' => 'https://bosadmin.bti360.io/logincallback.ashx',
			'state' => '0b41d7de39b0c7caf6c94314c0c44144ae93456c33d7eedf969fde4d63399b87;/',
			'nonce' => 'bad48dfd8c728e5e6aaf0aff6933841fae11b47111e5a5360b0fe7c87e34b84a',
			'response_mode' => 'form_post',
		);

		$logbody=$this->bodytool($reloginurl,$header,'GET',$jar,null,$query);

		$reloginurl1 = "https://bosadmin.bti360.io/logincallback.ashx";

		$post1 = array(
			'code' => $code,
			'id_token' => $id_token,
			'scope' => $scope,
			'state' => $state,
			'session_state' => $session_state,
		);
		$logbody=$this->bodytool($reloginurl1,$header,'POST',$jar,$post1);

		$reloginurl = "https://bosadmin.bti360.io/home_page/?referrer=home_page";

		$query = array(
			'referrer' => 'home_page',
		);

		$logbody=$this->bodytool($reloginurl,$header,'GET',$jar,null,$query);

		$reloginurl = "https://bosadmin.bti360.io/SportsPerformance?newPageCode=SportsPerformance";

		$query = array(
			'newPageCode' => 'SportsPerformance',
		);

		$logbody=$this->bodytool($reloginurl,$header,'GET',$jar,null,$query);
		$dom=new DOMDocument();
		@$dom->loadHTML($logbody);
		$select_prefix = $dom->getElementsByTagName('select')->item(2)->nodeValue;
		// $select_prefix = $dom->getElementsByTagName('select')->item(2)->getElementsByTagName('option')->item(0)->getAttribute('value');
		foreach ($dom->getElementsByTagName('select')->item(2)->getElementsByTagName('option') as $key => $value) {
			$prefix[$key]['prefix_number']=$value->getAttribute('value');
			$prefix[$key]['prefix']=$value->nodeValue;
		}
		$select_prefix = strtolower($select_prefix);
		$select_prefix = explode('bos',$select_prefix);
		$tmp_data=[];
		foreach ($prefix as $s => $t)
		{
			if ($t['prefix'])
			{
				$reloginurl = "https://bosadmin.bti360.io/ajaxmethods/pagemethods.aspx/ExecuteAction";

				$post2 = array(
					'widgetId' => '12926',
					'action' => 'GetData',
					'filterParameters' => '{"GroupBy":"Sport","ddlPeriod":"DateRange","ddlBrands":["'.$t['prefix_number'].'"],"ddlDisplayCurrencyFilter":"37","ddlDateFrom":"'.$s_date.'T00:00:00","ddlDateTo":"'.$e_date.'T23:59:59","chkIncludeTestAccounts":false,"chkIncludeFreeBets":false,"chkShowUniquePlayerData":false,"refferer":"Sport"}',
					'customParameters' => '{}',
				);

				$logbody=$this->bodytool($reloginurl,$header,'POST',$jar,$post2);
				$logbody = json_decode($logbody,true);

				foreach ($logbody['Data']['DataSourceData'][1] as $key => $value)
				{
					if ($value['Group'] == "Total")
					{
						$total[$key]['prefix'] = $t['prefix'];
						$total[$key]['payout'] = $value['GGR_SelectedCurrency'];
						preg_match('/bos(.+)/',$total[$key]['prefix'],$prefix);

						if ($total[$key]['payout'] != 0)
						{
							$tmp_data[] = [
								'prefix'=>$prefix[1],
								'payout'=>$total[$key]['payout'],
							];
						}
					}
				}
				
			}
		}
		foreach($tmp_data as $b){

          $na[]=$b['prefix'];

        }

		// print_r($tmp_data);
		return $tmp_data;
	}
}

// $x=new bti;
// $x->index('2019-11-01','2019-11-17');

