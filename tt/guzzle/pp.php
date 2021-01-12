<?php
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class pp extends tools{

	function index($s_date,$e_date,$game=null){

		if($game=='1'){
	  		$startdate=date_parse_from_format('Y-m-d', $s_date);  #date_parse_from_format()可以把日其格式化成字串
			$enddate=date_parse_from_format('Y-m-d', $e_date); 

			if($startdate['day']<10){							#小于10的数字前面加上0
				$startdate['day']='0'.$startdate['day'];
			}
			if($startdate['month']<10){
				$startdate['month']='0'.$startdate['month'];
			}
			if($enddate['day']<10){
				$enddate['day']='0'.$enddate['day'];
			}
			if($enddate['month']<10){
				$enddate['month']='0'.$enddate['month'];
			}
			$prefixlist=$this->prefixlist();

			$jar=new CookieJar;




			$url='https://backoffice-tw.pragmaticplay.net/admin/';
			$header=array(

				'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
				'content-type'=>'application/x-www-form-urlencoded',

			);
			$head=$this->headertool($url,'GET',$jar,$header,null);
			preg_match('/(JSESSIONID=.+?;)/',$head['Set-Cookie'][0],$cookie);
			// $allcookie='DWRSESSIONID=DgfNIEcU1NTPj0pNfx0KgeQBvGm; '.$cookie[0].' timeZoneID=28800000';
			//print_r($cookie);
			$loginurl='https://backoffice-tw.pragmaticplay.net/admin/firstPage.do;'.$cookie[0];
			$header2=array(

				'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
				'content-type'=>'application/x-www-form-urlencoded',
				//'cookie'=>$allcookie,

			);
			//print_r($header2);exit;
			$post=array(

				'login'=>'defeng_master_admin', 
				'password'=>'5MNydQkWTFuK', 
				'submitFromLoginPage'=>'true',

			);
			//print_r($header2);exit;
			$this->bodytool($loginurl,$header2,'POST',$jar,$post);

			$prefixdataurl='https://backoffice-tw.pragmaticplay.net/admin/memberBusinessSummary.do?searchOptions.from='.$startdate['day'].'%2F'.$startdate['month'].'%2F'.$startdate['year'].'&searchOptions.to='.$enddate['day'].'%2F'.$enddate['month'].'%2F'.$enddate['year'].'&searchOptions.playerID=&searchOptions.nickName=&searchOptions.signUpLanguageID=&searchOptions.customerID=1324&searchOptions.casinosID=2160&useStartRoundDate=true&searchOptions.groupBy=DD&searchOptions.criterion=&searchOptions.amount=0.0&searchOptions.currency=&pageNumber=&scrollPortion=0&scrollAction=&searchOptions.sortingColumn=period&searchOptions.sortingOrder=DESC&drillDown=false&method=search';
			$res=$this->bodytool($prefixdataurl,$header2,'GET',$jar,null);
			//print_r($res);exit;
			$dom =new DOMDocument();                       
			@$dom->loadHTML($res);                     
			$xpath = new DOMXPath($dom);
			$nodeList=$xpath->query('//*[contains(@name,"searchOptions.casinosID")]/option');
			foreach ($nodeList as $n) {

			    $prefixdata[]= [

			    	'id'=>$n->attributes->getNamedItem('value')->nodeValue,		#站台代码
			    	'name'=>$n->nodeValue,											#站台名称

		    	];
		    	// print_r($n->nodeValue.'done...');
		   	}
		   	//print_r($prefixdata);exit;
		   	$total=[];
		   	foreach ($prefixdata as $k=>$v) {
		   		//print_r($v);
		   		$reporturl = 'https://backoffice-tw.pragmaticplay.net/admin/gamesProfitSummaryReport.do?searchOptions.from='.$startdate['day'].'%2F'.$startdate['month'].'%2F'.$startdate['year'].'&searchOptions.customerID=1324&searchOptions.casinosID='.$v['id'].'&searchOptions.signUpLanguageID=&searchOptions.deviceType=&searchOptions.to='.$enddate['day'].'%2F'.$enddate['month'].'%2F'.$enddate['year'].'&searchOptions.groupBy=MM&useStartRoundDate=false&scrollPortion=0&scrollAction=&searchOptions.sortingColumn=period&searchOptions.sortingOrder=ASC&method=search';
		   		$report=$this->bodytool($reporturl,$header2,'GET',$jar,null);
		   		//print_r($report);exit;
		   		$dom =new DOMDocument();                       
				@$dom->loadHTML($report);                     
				$xpath = new DOMXPath($dom);
				$nodeList=$xpath->query('//*[contains(@class,"totals")]/td');
				//print_r($nodeList[3]->nodeValue);exit;
				$bet=trim($nodeList[12]->nodeValue); //月底產出報表用
				$payout=trim($nodeList[16]->nodeValue);
				$payout=str_replace(',','',$payout);
				if(preg_match("/bos/i",$v['name']) && $payout!=0){
					($v['name']=='bosjsh')? $v['name']='bosjss' : false ;
					preg_match("/bos(.+)/i",$v['name'],$prefix);
					$total[$prefix[1]]=[

						'prefix'=>$prefix[1],
						'payout'=>$payout,
						'bet'=>$bet, //月底產出報表用

					];
					// print_r($prefix[1]."\t".$bet."\t".$payout.PHP_EOL);//月底產出報表用
					// print_r($v['name'].'done...');
				}

			}
  		
			return $total;
			
		}else if ($game=='2') {
			$jar = new CookieJar;

		    $loginurl='https://casinoadmin.pragmaticplaylive.net/api/auth/login';
		    $header=[
		        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
		    	'Accept' => 'application/json, text/plain, */*',
		        'Accept-Encoding' => 'gzip, deflate, br',
		        'Accept-Language' =>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
		        'Connection'=>'keep-alive',
		    ];
		    $post=[
		    	'email' =>"csbg888@gmail.com",
		        'password' => "cSb$#@88",
		        'rememberMe' => false,
		    ];
		    $a=$this->bodytool($loginurl,$header,'POST',$jar,$post,"json");
    		$re = json_decode($a, true);
    		$access_token = $re['token']['access_token'];
    		//$refresh_token = $re['token']['refresh_token'];
           
            $header=[
                'content-type'=>'application/json',
                'authorization'=>'Bearer '.$access_token,
            ];
            
    		$post=[
                'casinoIds'=>["All"],
    			'defaultCurrency' => true,
    			'endDate' =>$e_date,
    			'gameTypeIds' => [],
    			'noOfRecords'=> 100000,
    			'orderBy' => "stake",
    			'orderByType'=> "desc",
    			'page' => 0,
    			'startDate'=> $s_date,
    			'tableIds'=> [],
    			'totalSize'=> 0,	
            ];
    		//report_url
    		$reurl='https://casinoadmin.pragmaticplaylive.net/api/financialReports/playerStatsReport';
            $report=$this->bodytool($reurl,$header,'POST',$jar,$post,"json");		
    		$report=json_decode($report, true);
            
    		
    		unset($prefixList);
    		foreach($report['reports'] as $k => $v){
                $prefix=str_replace(' ','', $v['casinoName']);
                preg_match('/PPDefengBOSbos(.+)/', $prefix,$prefix);
    			$prefix[1] = trim($prefix[1]);
    			if($prefix[1]=='jsh'){
    				$prefix[1]='jss';
    			}   
                if(isset($payout[$prefix[1]]))
    			{
    				$payout[$prefix[1]] = $payout[$prefix[1]] + $v['winnings'];
    			}
    			else
    			{
    				$prefixList[] = $prefix[1];
    				$payout[$prefix[1]]=$v['winnings'];
    			}
            }
    		
    		foreach($prefixList as $k => $v){
    			$total[]=[
    				'prefix' => $v,
    				'payout' => $payout[$v],
    			];
    		}
            return $total;
		}
		// exit;//月底產出報表用
		#pp捕魚不同後台
		// $url='https://crm.silverlighting.net/api/user/sign';
		// $header=[
		// 	'content-type'=>'application/x-www-form-urlencoded',
		// ];
		// $post=[
		// 	"verify_type"=>"LOGIN",
		// 	"secret"=>"eyJ1c2VybmFtZSI6IkRlZmVuZy1NYWluIiwicGFzc3dvcmQiOiJwcGZpc2hpbmcifQ==",
		// 	"authenticity_token"=>"1324sa6c",
		// ];
		// $this->bodytool($url,$header,'POST',$jar,json_encode($post),'body');
		// $reurl='https://crm.silverlighting.net/api/stats/player/statistics';
		// $post=[
		// 	"vendorID"=>10171,
		// 	"currency"=>"CNY",
		// 	"playerID"=>"",
		// 	"statTime"=>$s_date,
		// 	"endTime"=>$e_date,
		// 	"pageSize"=>1000,
		// 	"pageIdx"=>1,
		// ];
		// $result=$this->bodytool($reurl,$header,'POST',$jar,json_encode($post),'body');
		// $result=json_decode($result,1);

		// foreach ($prefixlist as $k1 => $v1) {
		// 	foreach ($result['timePlayerStatisticsResult'] as $k2 => $v2) {
		// 		$prefix=substr($v2['userName'], 0,strlen($v1));
		// 		$payout=$v2['incomeAmount'];
		// 		if($prefix==$v1){
		// 			if(isset($total[$prefix])){
		// 				$total[$prefix]['payout']+=$payout;
		// 			}else{
		// 				$total[$prefix]=[
		// 					'prefix'=>$prefix,
		// 					'payout'=>$payout,
		// 				];
		// 			}
					
		// 		}
		// 	}
		// }
		
		//echo '厂商账务捞取完毕'.PHP_EOL;      
		//print_r($total);exit;    //月底產出報表用

	}

} 
// $x=new pp;
// $x->index('2019-04-01','2019-04-30');