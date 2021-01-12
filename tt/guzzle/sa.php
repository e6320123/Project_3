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

class sa extends tools{

	public function index($s_date,$e_date,$game=null){


		$s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date("Y-m-d", strtotime("+1 day",strtotime($e_date)));


        if(file_exists('cookie.txt')){
            unlink('cookie.txt');
        }
		$jar = new CookieJar;

		//取得登入頁
		$login_page_url = 'https://www.sa-bo.net/';
		$header = array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		);
		$token = $this->bodytool($login_page_url, $header, 'GET', $jar, null);
		$dom = new DOMDocument();
		@$dom->loadHTML($token);
		$xpath = new DOMXpath($dom);
		//驗證碼參數
		$captcha_q = $xpath->query("//img[@class='LBD_CaptchaImage']");
		foreach ($captcha_q as $key => $value) {
		    $captcha_img[$key] = $value->getAttribute('src');
		}
		//登入參數
		$VIEWSTATE_q = $xpath->query("//input[@id='__VIEWSTATE']");
		foreach ($VIEWSTATE_q as $key => $value) {
		    $login_data['__VIEWSTATE'] = $value->getAttribute('value');
		}
		$VIEWSTATEGENERATOR_q = $xpath->query("//input[@id='__VIEWSTATEGENERATOR']");
		foreach ($VIEWSTATEGENERATOR_q as $key => $value) {
		    $login_data['__VIEWSTATEGENERATOR'] = $value->getAttribute('value');
		}
		$EVENTVALIDATION_q = $xpath->query("//input[@id='__EVENTVALIDATION']");
		foreach ($EVENTVALIDATION_q as $key => $value) {
		    $login_data['__EVENTVALIDATION'] = $value->getAttribute('value');
		}
		$LBD_VCID_c_login_loginuser_logincaptcha_q = $xpath->query("//input[@id='LBD_VCID_c_login_loginuser_logincaptcha']");
		foreach ($LBD_VCID_c_login_loginuser_logincaptcha_q as $key => $value) {
		    $login_data['LBD_VCID_c_login_loginuser_logincaptcha'] = $value->getAttribute('value');
		}
		preg_match_all('/(?<=_TSM_CombinedScripts_=)(.*)(?=" )/', $token, $Login_aspx);
		$login_data['Login_aspx'] = urldecode($Login_aspx['0']['0']);


		//取得驗證碼
		$captcha_url = 'https://www.sa-bo.net/'.$captcha_img[0];
		$header = array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		);
		$captcha = $this->bodytool($captcha_url, $header, 'GET', $jar, null);


		//登入
		$loginurl = 'https://www.sa-bo.net/';
		$post = array(
			'__LASTFOCUS'=>'',
            'ToolkitScriptManager1_HiddenField'=>$login_data['Login_aspx'],
            '__EVENTTARGET'=>'',
            '__EVENTARGUMENT'=>'',
            '__VIEWSTATE'=>$login_data['__VIEWSTATE'],
            '__VIEWSTATEGENERATOR'=>$login_data['__VIEWSTATEGENERATOR'],
            '__EVENTVALIDATION'=>$login_data['__EVENTVALIDATION'],
            'LoginUser$UserName'=>'boscs01',
            'LoginUser$Password'=>'123qwe',
            'LoginUser$CaptchaCodeTextBox'=>$this->captcha($captcha,150),
            'LBD_VCID_c_login_loginuser_logincaptcha'=>$login_data['LBD_VCID_c_login_loginuser_logincaptcha'],
            'LBD_BackWorkaround_c_login_loginuser_logincaptcha'=>'1',
            'LoginUser$LoginButton'=>'登入',
		);
		$login = $this->bodytool($loginurl, $header, 'POST', $jar, $post);
		

		//取得輸贏頁面
		$win_lose_url = 'https://www.sa-bo.net/Operator/WinLose2.aspx';		
		$win_lose_token = $this->bodytool($win_lose_url, $header, 'GET', $jar, null);
		preg_match_all('/(?<=_TSM_CombinedScripts_=)(.*)(?=" )/', $win_lose_token, $WinLose);
		$winlose_data['TSM'] = urldecode($WinLose['0']['0']);
		$dom = new DOMDocument();
		@$dom->loadHTML($win_lose_token);
		$xpath = new DOMXpath($dom);
		$VIEWSTATE_q = $xpath->query("//input[@id='__VIEWSTATE']");
		foreach ($VIEWSTATE_q as $key => $value) {
		    $winlose_data['__VIEWSTATE'] = $value->getAttribute('value');
		}

		$VIEWSTATEGENERATOR_q = $xpath->query("//input[@id='__VIEWSTATEGENERATOR']");
		foreach ($VIEWSTATEGENERATOR_q as $key => $value) {
		    $winlose_data['__VIEWSTATEGENERATOR'] = $value->getAttribute('value');
		}


		//取得總輸贏頁面
		$post = array(
			'ToolkitScriptManager1_HiddenField'=>$winlose_data['TSM'],
            '__EVENTTARGET'=>'',
            '__EVENTARGUMENT'=>'',
            '__LASTFOCUS'=>'',
            '__VIEWSTATE'=>$winlose_data['__VIEWSTATE'],
            '__VIEWSTATEGENERATOR'=>$winlose_data['__VIEWSTATEGENERATOR'],
            '__VIEWSTATEENCRYPTED'=>'',
            'hfbetsource'=>'',
            'tbFromDate'=>$s_date,
            'tbToDate'=>$e_date,
            'ddlRecordsPerPage'=>'100',
            'btnSearch'=>'查询',
            'cblBetSource$0'=>'4',
            'cblBetSource$1'=>'2',
            'hfoperatorName'=>'',
            'hfusername'=>'',
            'hflevel'=>'',
            'hfgamecategory'=>'',
            'hfcSSelfRefundRatio'=>'',
            'hfcSSelfProportion'=>'',
            'hfcSParentRefundRatio'=>'',
            'hfcSParentProportion'=>'',
            'hfname'=>'',
            'hfSortType'=>'1',
            'hfrowCount'=>'',
            'hfForExport'=>'false',
            'hfitemUserNamePaging'=>'',
            'hfisPlayerPaging'=>'',
            'hfselfrefundratioPaging'=>'',
            'hfselfproportionPaging'=>'',
            'hfIsParentLvChild4'=>'false',
            'hfIsParentLvChild1_5'=>'false',
		);
		$total_winlose = $this->bodytool($win_lose_url, $header, 'POST', $jar, $post);
		preg_match_all('/(?<=_TSM_CombinedScripts_=)(.*)(?=" )/', $total_winlose, $WinLose);
		$winlose_data['TSM'] = urldecode($WinLose['0']['0']);
		$dom = new DOMDocument();
		@$dom->loadHTML($total_winlose);
		$xpath = new DOMXpath($dom);
		$VIEWSTATE_q = $xpath->query("//input[@id='__VIEWSTATE']");
		foreach ($VIEWSTATE_q as $key => $value) {
		    $winlose_data['__VIEWSTATE'] = $value->getAttribute('value');
		}

		$VIEWSTATEGENERATOR_q = $xpath->query("//input[@id='__VIEWSTATEGENERATOR']");
		foreach ($VIEWSTATEGENERATOR_q as $key => $value) {
		    $winlose_data['__VIEWSTATEGENERATOR'] = $value->getAttribute('value');
		}


		//取得詳細輸贏頁面
		$post = array(
			'ToolkitScriptManager1_HiddenField'=>$winlose_data['TSM'],
			'__EVENTTARGET'=>'lvChild1_5$ctrl0$itemAcct',
			'__EVENTARGUMENT'=>'',
			'__VIEWSTATE'=>$winlose_data['__VIEWSTATE'],
			'__VIEWSTATEGENERATOR'=>$winlose_data['__VIEWSTATEGENERATOR'],
			'__VIEWSTATEENCRYPTED'=>'',
			'hfbetsource'=>'-1',
			'tbFromDate'=>$s_date,
			'tbToDate'=>$e_date,
			'ddlRecordsPerPage'=>'100',
			'cblBetSource$0'=>'4',
			'cblBetSource$1'=>'2',
			'hfoperatorName'=>'',
			'hfusername'=>'',
			'hflevel'=>'',
			'hfgamecategory'=>'',
			'hfcSSelfRefundRatio'=>'',
			'hfcSSelfProportion'=>'',
			'hfcSParentRefundRatio'=>'',
			'hfcSParentProportion'=>'',
			'hfname'=>'',
			'hfSortType'=>'1',
			'hfrowCount'=>'',
			'hfForExport'=>'false',
			'hfitemUserNamePaging'=>'',
			'hfisPlayerPaging'=>'',
			'hfselfrefundratioPaging'=>'',
			'hfselfproportionPaging'=>'',
			'hfIsParentLvChild4'=>'false',
			'hfIsParentLvChild1_5'=>'false',
			'__ASYNCPOST'=>'true',
			''=>'',
		);
		$detail_winlose = $this->bodytool($win_lose_url, $header, 'POST', $jar, $post);
		$dom = new DOMDocument();
		@$dom->loadHTML($detail_winlose);
		#echo $dom -> saveHTML();
		$xpath = new DOMXpath($dom);
		$t_q = $xpath->query("//table[@id='lvdfbos_lvsubLobby_Table1']//span");
		$p_n = 0;
		$name_n=0;
		$payout_n=4;
		foreach ($t_q as $key => $value) {
		    if($key < 10){

		    	if(substr($t_q->item($name_n)->nodeValue,3) != '' && str_replace(',', '', $t_q->item($payout_n)->nodeValue) != ''){
		    		$prefix=substr($t_q->item($name_n)->nodeValue,3);
		    		($prefix=='n888')?$prefix='n88':false;
		    		($prefix=='jsh')?$prefix='jss':false;
		    		($prefix=='8888')?$prefix='888':false;
		    		$total[$p_n]['prefix'] = $prefix;
		        	$total[$p_n]['payout'] = str_replace(',', '', $t_q->item($payout_n)->nodeValue);
		        	$p_n++;
		    	}
		    	
		    }
		    else{
		        $name_n += 10;
		        $payout_n += 10;

		        if(substr($t_q->item($name_n)->nodeValue,3) != '' && str_replace(',', '', $t_q->item($payout_n)->nodeValue) != ''){
		        	$prefix=substr($t_q->item($name_n)->nodeValue,3);
		        	($prefix=='n888')?$prefix='n88':false;
		        	($prefix=='jsh')?$prefix='jss':false;
		        	($prefix=='8888')?$prefix='888':false;
		    		$total[$p_n]['prefix'] = $prefix;
		        	$total[$p_n]['payout'] = str_replace(',', '', $t_q->item($payout_n)->nodeValue);
		        	$p_n++;
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

// $x=new sa();
// $x->index('2019-07-01','2019-07-02');