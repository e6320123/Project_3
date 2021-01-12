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
use GuzzleHttp\Cookie\SetCookie;
require_once 'class.tools.php';

class hb extends tools{

	public function index($s_date,$e_date,$game=null){
		date_default_timezone_set('Asia/Taipei');
		$sd = date('Y-m-d', strtotime($s_date));
        $ed = date('Y-m-d', strtotime('+1 days',strtotime($e_date)));

       
		$jar=new FileCookieJar('cookie.txt', TRUE);

		//取得登入頁面
		$login_token_url = 'https://bo-a.insvr.com/login.aspx';
		$header = [
			'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'upgrade-insecure-requests' => '1',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		];
		$login_token = $this->bodytool($login_token_url,$header,'GET',$jar,null);

		$arraccount = array(
			  '__LASTFOCUS'=>''
			, '__EVENTTARGET'=>''
			, '__EVENTARGUMENT'=>''
			, '__VIEWSTATE'=>''
			, '__VIEWSTATEGENERATOR'=>''
			, '__EVENTVALIDATION'=>''
			, 'loginControl$UserName'=>'BGFinance'
			, 'loginControl$Password'=>'aabb8899'
			, 'loginControl$LoginButton'=>'登錄'
		) ;
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($login_token, 'HTML-ENTITIES', 'UTF-8')) ;
		$arraccount['__VIEWSTATE'] = @$dom->getElementById('__VIEWSTATE')->getAttribute('value') ;
		$arraccount['__VIEWSTATEGENERATOR'] = @$dom->getElementById('__VIEWSTATEGENERATOR')->getAttribute('value') ;
		$arraccount['__EVENTVALIDATION'] = @$dom->getElementById('__EVENTVALIDATION')->getAttribute('value') ;

		$jar->setCookie(new SetCookie([
					'Name'    => '_culture',
					'Value'   => 'zh-TW',
					'Domain'  => 'bo-a.insvr.com',
					'Path'    => '/',
					'HttpOnly' => 1
				]));
		$jar->save('cookie.txt') ;

		//登入HB電子
		$login_url = 'https://bo-a.insvr.com/login.aspx';
		$login_token = $this->bodytool($login_url,$header,'POST',$jar,$arraccount);

		//取得參數
		$menu_token_url = 'https://bo-a.insvr.com/Form/Menu.aspx';
		$menu_token_header = [
			'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'upgrade-insecure-requests' => '1',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		];
		$menu_token = $this->bodytool($menu_token_url,$menu_token_header,'GET',$jar,null);
		$formdata = array(
			  'rsm'	=>	'radAjaxPanelPanel|selectGroupBrand$radComboBox'
			,'rsm_TSM'	=>	''
			,'__EVENTTARGET'	=>	''
			,'__EVENTARGUMENT'	=>	''
			,'__VIEWSTATE'	=>	''
			,'__VIEWSTATEGENERATOR'	=>	''
			,'selectGroupBrand$radComboBox'	=>	'BET'
			,'selectGroupBrand_radComboBox_ClientState'	=>	'{"logEntries":[],"value":"B|64815122-eba7-e611-80c4-000d3a805b30","text":"BET","enabled":true,"checkedIndices":[],"checkedItemsTextOverflows":false}'
			,'__ASYNCPOST'	=>	'true'
			,'RadAJAXControlID'	=>	'radAjaxPanel'
		) ;
		$dom = new DOMDocument();
		@$dom->loadHTML($menu_token);
		$xpath = new DOMXpath($dom);
		$formdata['__VIEWSTATE'] = $xpath->query('//*[@id="__VIEWSTATE"]/@value')->item(0)->nodeValue;
		$formdata['__VIEWSTATEGENERATOR'] = $xpath->query('//*[@id="__VIEWSTATEGENERATOR"]/@value')->item(0)->nodeValue;
		preg_match_all('/Telerik\.Web\.UI\.WebResource.axd\?\_TSM\_HiddenField\_\=rsm\_TSM\&amp\;compress\=1&amp\;\_TSM\_CombinedScripts\_\=\%3b\%3b(.*?)\" type\=\"text\/javascript\"\>/',$menu_token,$match) ;

		//
		$menu_url = 'https://bo-a.insvr.com/Form/Menu.aspx';
		$menu_header = [
			'accept' => '*/*',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
			'origin' => 'https://bo-a.insvr.com',
			'referer' => 'https://bo-a.insvr.com/form/Report/ByAccess/BrandSummary.aspx',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
			'x-microsoftajax' => 'Delta=true',
			'x-requested-with' => 'XMLHttpRequest'
		];
		$menu = $this->bodytool($menu_url,$menu_header,'POST',$jar,$formdata);

		//取得報表

		$data_token_url = 'https://bo-a.insvr.com/Form/Report/ByAccess/BrandSummary.aspx';
		$data_token_header = [
			'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'origin' => 'https://bo-a.insvr.com',
			'referer' => 'https://bo-a.insvr.com/Form/Menu.aspx',
			'upgrade-insecure-requests' => '1',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
		];
		$data_token = $this->bodytool($data_token_url,$data_token_header,'GET',$jar,null);
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($data_token, 'HTML-ENTITIES', 'UTF-8')) ;
		$xpath = new DOMXpath($dom);
		$setdata['rsm_TSM'] = $xpath->query('//*[@id="rsm_TSM"]/@value')->item(0)->nodeValue;
		$setdata['__VIEWSTATE'] = $xpath->query('//*[@id="__VIEWSTATE"]/@value')->item(0)->nodeValue;
		$setdata['__VIEWSTATEGENERATOR'] =  $xpath->query('//*[@id="__VIEWSTATEGENERATOR"]/@value')->item(0)->nodeValue;
		$setdata['__EVENTVALIDATION'] = $xpath->query('//*[@id="__EVENTVALIDATION"]/@value')->item(0)->nodeValue;


		
		$today = strtotime("now");

		$formdata_data= 'ctl00%24rsm=ctl00%24main%24ctl00%24main%24rpPanel%7Cctl00%24main%24btnGo&rsm_TSM='.rawurlencode($setdata['rsm_TSM']).'&ctl00_main_dd_ClientState=&ctl00_main_windowManager_ClientState=&ctl00%24main%24ucDateTime%24ddlDateSelector=Custom&ctl00%24main%24ucDateTime%24rdpStartDt='.$sd.'-00-00-00&ctl00%24main%24ucDateTime%24rdpStartDt%24dateInput='.date("d",strtotime($sd)).'%20'.date("M",strtotime($sd)).'%20'.date("Y",strtotime($sd)).'%2000%3A00&ctl00_main_ucDateTime_rdpStartDt_calendar_SD=%5B%5B'.date("Y",strtotime($sd)).'%2C'.date("n",strtotime($sd)).'%2C'.date("j",strtotime($sd)).'%5D%5D&ctl00_main_ucDateTime_rdpStartDt_calendar_AD=%5B%5B1980%2C1%2C1%5D%2C%5B2099%2C12%2C30%5D%2C%5B'.date("Y",$today).'%2C'.date("n",$today).'%2C'.date("j",$today).'%5D%5D&ctl00_main_ucDateTime_rdpStartDt_timeView_ClientState=&ctl00_main_ucDateTime_rdpStartDt_dateInput_ClientState=%7B%22enabled%22%3Atrue%2C%22emptyMessage%22%3A%22%22%2C%22validationText%22%3A%22'.$sd.'-00-00-00%22%2C%22valueAsString%22%3A%22'.$sd.'-00-00-00%22%2C%22minDateStr%22%3A%221980-01-01-00-00-00%22%2C%22maxDateStr%22%3A%222099-12-31-00-00-00%22%2C%22lastSetTextBoxValue%22%3A%22'.date("d",strtotime($sd)).'%20'.date("M",strtotime($sd)).'%20'.date("Y",strtotime($sd)).'%2000%3A00%22%7D&ctl00_main_ucDateTime_rdpStartDt_ClientState=&ctl00%24main%24ucDateTime%24rdpEndDt='.$ed.'-00-00-00&ctl00%24main%24ucDateTime%24rdpEndDt%24dateInput='.date("d",strtotime($ed)).'%20'.date("M",strtotime($ed)).'%20'.date("Y",strtotime($ed)).'%2000%3A00&ctl00_main_ucDateTime_rdpEndDt_calendar_SD=%5B%5B'.date("Y",strtotime($ed)).'%2C'.date("n",strtotime($ed)).'%2C'.date("j",strtotime($ed)).'%5D%5D&ctl00_main_ucDateTime_rdpEndDt_calendar_AD=%5B%5B1980%2C1%2C1%5D%2C%5B2099%2C12%2C30%5D%2C%5B'.date("Y",$today).'%2C'.date("n",$today).'%2C'.date("j",$today).'%5D%5D&ctl00_main_ucDateTime_rdpEndDt_timeView_ClientState=&ctl00_main_ucDateTime_rdpEndDt_dateInput_ClientState=%7B%22enabled%22%3Atrue%2C%22emptyMessage%22%3A%22%22%2C%22validationText%22%3A%22'.$ed.'-00-00-00%22%2C%22valueAsString%22%3A%22'.$ed.'-00-00-00%22%2C%22minDateStr%22%3A%221980-01-01-00-00-00%22%2C%22maxDateStr%22%3A%222099-12-31-00-00-00%22%2C%22lastSetTextBoxValue%22%3A%22'.date("d",strtotime($ed)).'%20'.date("M",strtotime($ed)).'%20'.date("Y",strtotime($ed)).'%2000%3A00%22%7D&ctl00_main_ucDateTime_rdpEndDt_ClientState=&ctl00%24main%24ucDateTime%24selectTimeZone%24ddlTimeZone=(GMT-04%3A00)%20Georgetown%2C%20La%20Paz%2C%20San%20Juan%20%5BSA%20Western%20Standard%20Time%5D&ctl00_main_ucDateTime_selectTimeZone_ddlTimeZone_ClientState=%7B%22logEntries%22%3A%5B%5D%2C%22value%22%3A%2272%22%2C%22text%22%3A%22(GMT-04%3A00)%20Georgetown%2C%20La%20Paz%2C%20San%20Juan%20%5BSA%20Western%20Standard%20Time%5D%22%2C%22enabled%22%3Atrue%2C%22checkedIndices%22%3A%5B%5D%2C%22checkedItemsTextOverflows%22%3Afalse%7D&ctl00%24main%24selectCurrency%24ddlCurrency=China%20Yuan%20Renminbi%20%5BCNY%5D&ctl00_main_selectCurrency_ddlCurrency_ClientState=&ctl00%24main%24rdbtnGroupList=1&ctl00_main_RADDataGrid_rghcMenu_ClientState=&ctl00_main_RADDataGrid_ClientState=&__EVENTTARGET=ctl00$main$btnGo&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE='.rawurlencode($setdata['__VIEWSTATE']).'&__VIEWSTATEGENERATOR='.$setdata['__VIEWSTATEGENERATOR'].'&__EVENTVALIDATION='.rawurlencode($setdata['__EVENTVALIDATION']).'&__ASYNCPOST=true&RadAJAXControlID=ctl00_main_rp';

		
		$data_url = 'https://bo-a.insvr.com/Form/Report/ByAccess/BrandSummary.aspx';
		$data_header = [
			'accept' => '*/*',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
			'origin' => 'https://bo-a.insvr.com',
			'referer' => 'https://bo-a.insvr.com/Form/Report/ByAccess/BrandSummary.aspx',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
			'x-microsoftajax' => 'Delta=true',
			'x-requested-with' => 'XMLHttpRequest'
		];
		$data = $this->bodytool($data_url,$data_header,'POST',$jar,$formdata_data,'body');
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8')) ;
		$xpath = new DOMXpath($dom);
		$trs = $xpath->query('//*[@id="ctl00_main_RADDataGrid_ctl00"]/tbody/tr');
		
		$site = '' ;
		$n = 0;
		foreach($trs as $tr)
		{	
			//print_r($tr);

			$strtmp =trim($tr->getElementsByTagName('td')->item(1)->nodeValue) ;
			if(empty($strtmp) || $strtmp ==' ')
				continue ;
			else if($strtmp=='CNY')
			{
				$total[$n]['prefix'] = $site;
				$total[$n]['payout'] = (float)str_replace(",","",trim($tr->getElementsByTagName('td')->item(5)->nodeValue)) ;
				$n++;
			}
			else
			{
				$arrtmp = explode("-",$strtmp) ;
				$site = strtolower(trim($arrtmp[1])) ;
				if($site=='wwy') $site = "vip" ;
				else if($site=='jsh') $site = "jss" ;
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

// $x=new hb;
// $x->index('2019-07-01','2019-07-31');
