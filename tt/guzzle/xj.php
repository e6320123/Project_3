<?php 
//require_once __DIR__ . '/../../../vendor/autoload.php';
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

class xj extends tools{

	public function index($s_date,$e_date,$game=null){


		$s_date = date('d/m/Y', strtotime($s_date));
        $e_date = date('d/m/Y', strtotime($e_date));
        
        $prefix = $this->prefixlist();
        foreach ($prefix as $key => $value) {
        	if(strlen($value) == 4){
        		$four_arr[] = $value;
        	}
        }

		$jar = new CookieJar;


		


		//登入
		$login_url = 'https://opc.xj-platform.com/api/Authentication';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',
		];
		$json = array(
			'code'=>'',
            'customerCode'=>'cccccO',
            'password'=>'9wsNTGpABUl2',
            'usercode'=>'BOSCS',
		);
		$login = $this->bodytool($login_url, $header, 'POST', $jar, $json, 'json');
        

        //進入XJSport
        $lanuch_url = 'http://opc.xj-platform.com/api/opc/launch';
        $lunch_json = array(
            'brandId'=>'48',
            'productId'=>'1001',
        );
        $launch = $this->bodytool($lanuch_url, $header, 'POST', $jar, $lunch_json, 'json');
        $launch = json_decode($launch, ture);
     
        $launch_url=$launch['Data'];
        $header = [
            
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',
        ];
        $launch_page = $this->bodytool($launch_url, $header, 'GET', $jar, null);
       
		$get_data_url = 'http://opc-wl.prdasbbwla1.com/zh-cn/Reports/LedgerMember/';
		$header = [

			'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',
		];

		$query = array(
			'currencyId' => '9',
            'currencyCode' => '人民币',
			'dateFrom' => $s_date,
			'dateTo' => $e_date,
			'sportName' => 'All',
            'marketTypeGroupId' => '-1',
			'marketTypeGroupName' => '全部投注类别群组',
			'parentCurrencyId' => '9',
		);
		$get_data = $this->bodytool($get_data_url, $header, 'GET', $jar, null, null, $query);

		$getdata = new DOMDocument();
        libxml_use_internal_errors(true);
        $getdata->loadHTML('<?html encoding="utf-8" ?>'.$get_data);
        $trs = $getdata->getElementsByTagName('tr');

        $xj_detail_arr = [];
        $xj_arr = [];

        foreach($trs AS $tr){
            if($tr->getAttribute('class') != 'o' AND $tr->getAttribute('class') != 'e')continue;
            $tds = $tr->getElementsByTagName('td');
            $member_name = trim($tds->item(1)->nodeValue);
            $member_name = strtolower($member_name);
            $name = trim($tds->item(1)->nodeValue);
            if(in_array(substr($name,0,4),$four_arr)){
                $name = substr($name,0,4);
            }else{
                $name = substr($name,0,3);
            }
            $name = strtolower($name);
            $money = trim($tds->item(5)->nodeValue);
            $money = str_replace(',','',$money);
            $xj_detail_arr[$name][$member_name] = $money;
            if(isset($xj_arr[$name])){
                $xj_arr[$name] += $money;
            }else{
                $xj_arr[$name] = $money;
            }
        }

        $n = 0;
        foreach ($xj_arr as $key => $value) {
        	$total[$n]['prefix'] = $key;
        	$total[$n]['payout'] = $value;	   
        	$n++;     
       	}
 
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);

        return $total;

	}

    public function index_detail($s_date,$e_date,$game=null,$plat){

        
        $s_date = date('d/m/Y', strtotime($s_date));
        $e_date = date('d/m/Y', strtotime($e_date));
        
        // $prefix = $this->prefixlist();
        foreach ($prefix as $key => $value) {
            if(strlen($value) == 4){
                $four_arr[] = $value;
            }
        }

        $jar = new CookieJar;


        //登入
        $login_url = 'https://opc.xj-platform.com/api/Authentication';
        $header = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
        ];
        $json = array(
            'code'=>'',
            'customerCode'=>'cccccO',
            'password'=>'9wsNTGpABUl2',
            'usercode'=>'BOSCS',
        );
        $login = $this->bodytool($login_url, $header, 'POST', $jar, $json, 'json');

        //進入XJSport
        $lanuch_url = 'http://opc.xj-platform.com/api/OPC/LaunchOPC';
        $lunch_json = array(
            'Product'=>'AgileBet',
            'RoleId'=>'2',
            'UserCode'=>'BOSCS',
            'XjOpCode'=>'BGE',
        );
        $launch = $this->bodytool($lanuch_url, $header, 'POST', $jar, $lunch_json, 'json');

        $launch = json_decode($launch, ture);
     
        $launch_url=$launch['Data'];
        $header = [
            
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
        ];
        $launch_page = $this->bodytool($launch_url, $header, 'GET', $jar, null);


        $get_data_url = 'http://opc-wl.prdasbbwla1.com/zh-cn/Reports/LedgerMember/';
        $header = [

            'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',
        ];

        $query = array(
            'currencyId' => '9',
            'currencyCode' => '人民币',
            'dateFrom' => $s_date,
            'dateTo' => $e_date,
            'sportName' => 'All',
            'marketTypeGroupId' => '-1',
            'marketTypeGroupName' => '全部投注类别群组',
            'parentCurrencyId' => '9',
        );
        $get_data = $this->bodytool($get_data_url, $header, 'GET', $jar, null, null, $query);
        $getdata = new DOMDocument();
        libxml_use_internal_errors(true);
        $getdata->loadHTML('<?html encoding="utf-8" ?>'.$get_data);
        $trs = $getdata->getElementsByTagName('tr');

        $total = [];
        $n = 0;
        foreach($trs AS $tr){
            if($tr->getAttribute('class') != 'o' AND $tr->getAttribute('class') != 'e')continue;
            $tds = $tr->getElementsByTagName('td');
            $member_name = trim($tds->item(1)->nodeValue);
            $member_name = strtolower($member_name);
            $name = trim($tds->item(1)->nodeValue);
            if(substr($name,0,strlen($plat)) == $plat){
                $money = trim($tds->item(5)->nodeValue);
                $money = str_replace(',','',$money);
                $total[$n]['memberCode'] = $member_name;
                $total[$n]['payout'] = $money;
                $n++;
            }

        }

        
        // print_r($total);
        return $total;

    }
}

// $x=new xj();
// $x->index('2019-08-15','2019-08-15');