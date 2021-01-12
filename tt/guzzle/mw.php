<?php   
//require_once 'vendor/autoload.php';
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php'; 

class mw extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;
		$loginurl='https://www.go88game.com/as-manage/login.do';
		$header=array(
			'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			'Accept-Encoding' => 'gzip, deflate',
			'Accept-Language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'Content-Type'	=>	'application/x-www-form-urlencoded',
			'Host' => 'www.go88game.com',
			'Origin' => 'https://www.go88game.com',
			'Referer' => 'https://www.go88game.com/as-manage/',
			'Upgrade-Insecure-Requests' => '1',
		);
		$post=array(
			'userName'=>'BOS002',
			'password'=>'50b167561a1fcae17df1c0a9026b7fa9',
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);//exit;
		//print_r($head['Set-Cookie'][0]);exit;
		// preg_match("/.+;/i",$head['Set-Cookie'][1],$cookie);
		// preg_match("/.+;/i",$head['Set-Cookie'][0],$cookie2);

		$reurl='https://www.go88game.com/as-manage/gameReport/listSgmInfos.do';
		$header2=array(
			'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			'Accept-Encoding' => 'gzip, deflate',
			'Accept-Language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'Content-Type'	=>	'application/x-www-form-urlencoded',
			'Host' => 'www.go88game.com',
			'Origin' => 'https://www.go88game.com',
			'Referer' => 'https://www.go88game.com/as-manage/',
			'Upgrade-Insecure-Requests' => '1',

			 //'Cookie'=>'JSESSIONID=F251AC5C228FF78D3DC3AFC4BB2C1EB0; gc-login-identity=579e6ae1f99d82b854dd9304c6a603d859194ae5ce7c0668c53605e29daa1b24ee4e485c0a4cfd188cf1cde252aa5aef8cf1cde252aa5aef8cf1cde252aa5aef8cf1cde252aa5aef6e8662b6d7f37c27',
			//'Cookie'=>$cookie[0].' '.$cookie2[0],
		);
		//print_r($header2);exit;
		$post2=array(
			'pagination.propertyName'=>'',
			'pagination.ascending'=>1,
			'pagination.currentPageNumber'=>1,
			'pagination.maxItemsPerPage'=>25,
			'platformIdQuery'=>'-1',
			'siteIdQuery'=>'-1',
			'isMobileQuery'=>'-1',
			'dateType'=>0,
			'beginDateQuery'=>$s_date.' 00:00:00',
			'endDateQuery'=>$e_date.' 23:59:59',
		);
		$body=$this->bodytool($reurl,$header2,'POST',$jar,$post2);
		//print_r($body);exit;
		//file_put_contents('mw.xml',$body);
		$dom =new DOMDocument();                       
		@$dom->loadHTML($body);                     
		$trs=$dom->getElementById('mainContainer')->getElementsByTagName('tr');

		foreach($trs as $k => $v){

			$prefix=trim($v->getElementsByTagName('td')->item(0)->nodeValue);
			$payout=$v->getElementsByTagName('td')->item(5)->nodeValue;
			$discount=$v->getElementsByTagName('td')->item(4)->nodeValue;

			if(preg_match("/bos/",$prefix) && $payout!=0){

				if($prefix=='bosxypw'){
					$prefix='bosypw';
				}
				if($prefix=='bosjsh'){
					$prefix='bosjss';
				}
				preg_match("/bos(.+)/",$prefix,$prefix);

				$total[$k]=[

					'prefix'=>$prefix[1],
					'payout'=>$payout+$discount,	

				];
			}
		}
		//print_r($total);exit;
		foreach($total as $b){ 

          $na[]=$b['prefix'];

        }
        array_multisort($na, SORT_ASC, $total);

		foreach($total as $v) {						#先用站台名称作索引把重复站的帐务相加，再用array_value()把索引变成数字

		    if(isset($su[$v['prefix']])) {

				$su[$v['prefix']]['payout'] += $v['payout'];
				
			}
			else{
				$su[$v['prefix']]=$v;
			}
			
		}
		$result=array_values($su);
		//print_r($result);exit;
		return $result;
		//print_r($result);exit;
	}

	function bodytool($url,$header,$method,$cookie,$post=null,$type='form_params',$query=null){								
     
	      $client=new Client(array(   
	          'cert' => dirname(dirname(__FILE__)).'/User2020.pem',
	          'verify' => dirname(dirname(__FILE__)).'/ca.crt',       
	          'cookies' => $cookie,
	          'verify' => false,
	          'proxy'=>'10.1.0.5:3128',
	      ));
	   

	      if($post!=null){

	    	    $res =$client->request($method,$url,[					
	    	    	'headers' => $header,
	            	"$type"=>$post,
	    	    ]);

	      }else{

	          $res =$client->request($method,$url,[          
	              'headers' => $header,
	          ]);

	      }

	 	  $body =$res->getbody()->getcontents();	
	      //echo $res->getStatusCode();			
	      return $body;

	}
}	

// $x=new mw;
// $x->index();