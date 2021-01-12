<?php
//require_once('../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class flow extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('d-m-Y', strtotime($s_date)) ;
		$e_date = date('d-m-Y', strtotime($e_date)) ;
		$conversionid='14172';  #币值id，查询报表会用到
		$jar= new CookieJar;
		$loginurl='https://cms.adminfg.com/j/Login.action';
		$header=array(

			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
		);
		$loginpage=$this->bodytool($loginurl,$header,'GET',$jar);
		$dom=new DOMDocument;
		@$dom->loadHTML(mb_convert_encoding($loginpage, 'HTML-ENTITIES', 'UTF-8'));
		$inps = @$dom->getElementsByTagName('input') ;
		$sourcePage = $inps->item(5)->getAttribute('value') ;
		$fp = $inps->item(6)->getAttribute('value') ;
		$post=array(
			'timeout'=>'',
			'core4Url'=>'',
			'loginName'=>'BGE.admin',
			'loginPassword'=>'4Y4YpHpU',
			'handleValidationErrors'=>'',
			'_sourcePage'=>$sourcePage,
			'__fp'=>$fp,
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);
		
		$reurl='https://cms.adminfg.com/j/report/revenue/RevenueByBrand.action';
		$header2=array(
			'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'content-type' => 'application/x-www-form-urlencoded',
			'upgrade-insecure-requests' => '1',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
		);
		$repage=$this->bodytool($reurl,$header2,'GET',$jar);
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($repage, 'HTML-ENTITIES', 'UTF-8')) ;
		$inps = @$dom->getElementsByTagName('form')->item(0)->getElementsByTagName('input') ;
		$sourcePage = $inps->item(8)->getAttribute('value') ;
		$fp = $inps->item(9)->getAttribute('value') ;
		$opts = @$dom->getElementById("brands")->getElementsByTagName("option") ;
		foreach($opts as $opt)
		{
			$arrsite[] = $opt->getAttribute('value') ;
		}
		$strbrands = "brands=".implode("&brands=",$arrsite) ;
		$formdata ='startDate='.$s_date.'&endDate='.$e_date.'&dateMode=REVENUE&'.$strbrands.'&platforms=1&platforms=4&platforms=5&platforms=7&platforms=8&platforms=11&platforms=12&platforms=13&platforms=14&platforms=15&currency=EUR&currencyMulti=TWD&currencyMulti=EUR&currencyMulti=MYR&currencyMulti=USD&currencyMulti=ZAR&currencyMulti=INR&currencyMulti=VND&currencyMulti=CNY&currencyMulti=THB&currencyMulti=KRW&currencyMulti=JPY&currencyMulti=IDR&currencyMulti=TRY&convertCheck=on&currencyConvDate='.$e_date.'&currencyConvVal=CNY&currencyConversionId='.$conversionid.'&fundTypes=RELEASED_BONUS&fundTypes=REAL&fundTypes=PLAYABLE_BONUS&vipLevels=-1&countries=AF&countries=AX&countries=AL&countries=DZ&countries=AS&countries=AD&countries=AO&countries=AI&countries=AQ&countries=AG&countries=AR&countries=AM&countries=AW&countries=AU&countries=AT&countries=AZ&countries=BS&countries=BH&countries=BD&countries=BB&countries=BY&countries=BE&countries=BZ&countries=BJ&countries=BM&countries=BT&countries=BO&countries=BA&countries=BW&countries=BV&countries=BR&countries=IO&countries=BN&countries=BG&countries=BF&countries=BI&countries=KH&countries=CM&countries=CA&countries=CV&countries=KY&countries=CF&countries=TD&countries=CL&countries=CN&countries=CX&countries=CC&countries=CO&countries=KM&countries=CG&countries=CD&countries=CK&countries=CR&countries=CI&countries=HR&countries=CU&countries=CY&countries=CZ&countries=DK&countries=DJ&countries=DM&countries=DO&countries=EC&countries=EG&countries=SV&countries=GQ&countries=ER&countries=EE&countries=ET&countries=FK&countries=FO&countries=FJ&countries=FI&countries=FR&countries=GF&countries=PF&countries=TF&countries=GA&countries=GM&countries=GE&countries=DE&countries=GH&countries=GI&countries=GR&countries=GL&countries=GD&countries=GP&countries=GU&countries=GT&countries=GG&countries=GN&countries=GW&countries=GY&countries=HT&countries=HM&countries=VA&countries=HN&countries=HK&countries=HU&countries=IS&countries=IN&countries=ID&countries=IR&countries=IQ&countries=IE&countries=IM&countries=IL&countries=IT&countries=JM&countries=JP&countries=JE&countries=JO&countries=KZ&countries=KE&countries=KI&countries=KP&countries=KR&countries=KW&countries=KG&countries=LA&countries=LV&countries=LB&countries=LS&countries=LR&countries=LY&countries=LI&countries=LT&countries=LU&countries=MO&countries=MK&countries=MG&countries=MW&countries=MY&countries=MV&countries=ML&countries=MT&countries=MH&countries=MQ&countries=MR&countries=MU&countries=YT&countries=MX&countries=FM&countries=MD&countries=MC&countries=MN&countries=ME&countries=MS&countries=MA&countries=MZ&countries=MM&countries=NA&countries=NR&countries=NP&countries=NL&countries=AN&countries=NC&countries=NZ&countries=NI&countries=NE&countries=NG&countries=NU&countries=NF&countries=MP&countries=NO&countries=OM&countries=PK&countries=PW&countries=PS&countries=PA&countries=PG&countries=PY&countries=PE&countries=PH&countries=PN&countries=PL&countries=PT&countries=PR&countries=QA&countries=RE&countries=RO&countries=RU&countries=RW&countries=BL&countries=SH&countries=KN&countries=LC&countries=MF&countries=PM&countries=VC&countries=WS&countries=SM&countries=ST&countries=SA&countries=SN&countries=RS&countries=SC&countries=SL&countries=SG&countries=SK&countries=SI&countries=SB&countries=SO&countries=ZA&countries=GS&countries=ES&countries=LK&countries=SD&countries=SR&countries=SJ&countries=SZ&countries=SE&countries=CH&countries=SY&countries=TW&countries=TJ&countries=TZ&countries=TH&countries=TL&countries=TG&countries=TK&countries=TO&countries=TT&countries=TN&countries=TR&countries=TM&countries=TC&countries=TV&countries=UG&countries=UA&countries=AE&countries=GB&countries=US&countries=UM&countries=UY&countries=UZ&countries=VU&countries=VE&countries=VN&countries=VG&countries=VI&countries=WF&countries=EH&countries=YE&countries=ZM&countries=ZW&execute=Go&_sourcePage='.$sourcePage.'&__fp='.$fp ;

		//print_r($formdata);exit;
		$report=$this->bodytool($reurl,$header2,'POST',$jar,$formdata,'body');
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($report, 'HTML-ENTITIES', 'UTF-8')) ;
		$trs = @$dom->getElementById('revenue_consolidated')->getElementsByTagName('tr') ;
		foreach($trs as $k =>$tr)
		{
			$prefix=$tr->getElementsByTagName('td')->item(0)->nodeValue;
			$payout=str_replace('CNY','',trim($tr->getElementsByTagName('td')->item(2)->nodeValue)) ;			
			if(preg_match("/bge/i",$prefix)){
				preg_match("/bge(.+)/i",$prefix,$prefix);
				$total[$k]=[
					'prefix' => $prefix[1],
					'payout' => $payout,
				];

			}
			
		}
		return $total;

	}


	public function index_detail($s_date,$e_date,$game=null,$plat){

		$s_date = date('d-m-Y', strtotime($s_date)) ;
		$e_date = date('d-m-Y', strtotime($e_date)) ;
		$jar= new CookieJar;
		$loginurl='https://cms.adminfg.com/j/Login.action';
		$header=array(

			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
		);
		$loginpage=$this->bodytool($loginurl,$header,'GET',$jar);
		$dom=new DOMDocument;
		@$dom->loadHTML(mb_convert_encoding($loginpage, 'HTML-ENTITIES', 'UTF-8'));
		$inps = @$dom->getElementsByTagName('input') ;
		$sourcePage = $inps->item(5)->getAttribute('value') ;
		$fp = $inps->item(6)->getAttribute('value') ;
		$post=array(
			'timeout'=>'',
			'core4Url'=>'',
			'loginName'=>'BGE.admin',
			'loginPassword'=>'4Y4YpHpU',
			'handleValidationErrors'=>'',
			'_sourcePage'=>$sourcePage,
			'__fp'=>$fp,
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);

		$reurl='https://cms.adminfg.com/j/report/revenue/RevenueByPlayer.action';
		$header2=array(
			'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'accept-encoding' => 'gzip, deflate',
			'accept-language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'content-type' => 'application/x-www-form-urlencoded',
			'upgrade-insecure-requests' => '1',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36',
		);
		$repage=$this->bodytool($reurl,$header2,'GET',$jar);
		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($repage, 'HTML-ENTITIES', 'UTF-8')) ;
		$xpath = new DOMXpath($dom);

		$query = $xpath->query("//select[@id='brands']/option");
		foreach ($query as $key => $value) {
			$id[$value->getAttribute("value")] = strtolower(substr($value->nodeValue,3));
		}
		$fp = $xpath->query("//input[@name='__fp']")->item(0)->getAttribute('value');
		$sourcePage = $xpath->query("//input[@name='_sourcePage']")->item(0)->getAttribute('value');

		$platid = array_search($plat, $id);
		
		$usrurl='https://cms.adminfg.com/j/report/revenue/RevenueByPlayer.action';
		$postdata = 'embeddedInCore4=false&startDate='.$s_date.'&endDate='.$e_date.'&dateMode=REVENUE&brands='.$platid.'&platforms=15&platforms=11&platforms=14&platforms=10004&platforms=12&platforms=10008&platforms=10007&platforms=1&platforms=8&platforms=10000&platforms=13&platforms=4&platforms=5&platforms=7&platforms=10005&currency=EUR&currencyMulti=CNY&convertCheck=on&currencyConvDate=16-11-2019&currencyConvVal=CNY&currencyConversionId=15488&fundTypes=PLAYABLE_BONUS&fundTypes=REAL&fundTypes=RELEASED_BONUS&vipLevels=-1&countries=AF&countries=AX&countries=AL&countries=DZ&countries=AS&countries=AD&countries=AO&countries=AI&countries=AQ&countries=AG&countries=AR&countries=AM&countries=AW&countries=AU&countries=AT&countries=AZ&countries=BS&countries=BH&countries=BD&countries=BB&countries=BY&countries=BE&countries=BZ&countries=BJ&countries=BM&countries=BT&countries=BO&countries=BA&countries=BW&countries=BV&countries=BR&countries=IO&countries=BN&countries=BG&countries=BF&countries=BI&countries=KH&countries=CM&countries=CA&countries=CV&countries=KY&countries=CF&countries=TD&countries=CL&countries=CN&countries=CX&countries=CC&countries=CO&countries=KM&countries=CG&countries=CD&countries=CK&countries=CR&countries=CI&countries=HR&countries=CU&countries=CY&countries=CZ&countries=DK&countries=DJ&countries=DM&countries=DO&countries=EC&countries=EG&countries=SV&countries=GQ&countries=ER&countries=EE&countries=ET&countries=FK&countries=FO&countries=FJ&countries=FI&countries=FR&countries=GF&countries=PF&countries=TF&countries=GA&countries=GM&countries=GE&countries=DE&countries=GH&countries=GI&countries=GR&countries=GL&countries=GD&countries=GP&countries=GU&countries=GT&countries=GG&countries=GN&countries=GW&countries=GY&countries=HT&countries=HM&countries=VA&countries=HN&countries=HK&countries=HU&countries=IS&countries=IN&countries=ID&countries=IR&countries=IQ&countries=IE&countries=IM&countries=IL&countries=IT&countries=JM&countries=JP&countries=JE&countries=JO&countries=KZ&countries=KE&countries=KI&countries=KP&countries=KR&countries=KW&countries=KG&countries=LA&countries=LV&countries=LB&countries=LS&countries=LR&countries=LY&countries=LI&countries=LT&countries=LU&countries=MO&countries=MK&countries=MG&countries=MW&countries=MY&countries=MV&countries=ML&countries=MT&countries=MH&countries=MQ&countries=MR&countries=MU&countries=YT&countries=MX&countries=FM&countries=MD&countries=MC&countries=MN&countries=ME&countries=MS&countries=MA&countries=MZ&countries=MM&countries=NA&countries=NR&countries=NP&countries=NL&countries=AN&countries=NC&countries=NZ&countries=NI&countries=NE&countries=NG&countries=NU&countries=NF&countries=MP&countries=NO&countries=OM&countries=PK&countries=PW&countries=PS&countries=PA&countries=PG&countries=PY&countries=PE&countries=PH&countries=PN&countries=PL&countries=PT&countries=PR&countries=QA&countries=RE&countries=RO&countries=RU&countries=RW&countries=BL&countries=SH&countries=KN&countries=LC&countries=MF&countries=PM&countries=VC&countries=WS&countries=SM&countries=ST&countries=SA&countries=SN&countries=RS&countries=SC&countries=SL&countries=SG&countries=SK&countries=SI&countries=SB&countries=SO&countries=ZA&countries=GS&countries=ES&countries=LK&countries=SD&countries=SR&countries=SJ&countries=SZ&countries=SE&countries=CH&countries=SY&countries=TW&countries=TJ&countries=TZ&countries=TH&countries=TL&countries=TG&countries=TK&countries=TO&countries=TT&countries=TN&countries=TR&countries=TM&countries=TC&countries=TV&countries=UG&countries=UA&countries=AE&countries=GB&countries=US&countries=UM&countries=UY&countries=UZ&countries=VU&countries=VE&countries=VN&countries=VG&countries=VI&countries=WF&countries=EH&countries=YE&countries=ZM&countries=ZW&execute=Go&_sourcePage='.$sourcePage.'&__fp='.$fp;
		$report=$this->bodytool($usrurl,$header2,'POST',$jar,$postdata,'body');

		$dom =new domDocument ;
		@$dom->loadHTML(mb_convert_encoding($report, 'HTML-ENTITIES', 'UTF-8')) ;
		$xpath = new DOMXpath($dom);
		$query = $xpath->query("//table[@id='revenue']//tbody/tr/td");
		$id_k = 2;
		$pay_k = 8;
		$n = 0;
		foreach ($query as $key => $value) {
			if($id_k == $key){
				$total[$n]['memberCode'] = trim($value->nodeValue);
				$id_k += 16;
			}
			if($pay_k == $key){
				$total[$n]['payout'] = str_replace(',','',str_replace('CNY','',trim($value->nodeValue)));
				$pay_k+=16;
				$n++;
			}

		}
		return $total;

	}

}
// $x=new flow;
// $x->index_detail('2019-11-16','2019-11-16',null,'v99');