<?php 
ini_set('max_execution_time','300');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';
class bbin extends tools{

	public function index($s_date,$e_date,$game=null){


		switch ($game) {
			case '1':
				$gameid=3;//真人
				break;
			case '2':
				$gameid=1;//體育
				break;
			case '3':
				$gameid=31;//新體育
				break;
			case '4':
				$gameid=12;//彩票
				break;	
			case '5':
				$gameid=30;//捕魚
				break;
			case '6':
				$gameid=38;//捕魚大師
				break;
			case '7':
				$gameid=99;//小費
				break;
			case '8':
				$gameid=5;//電子
				break;
			default:
				# code...
				break;
		}
		$allprefix=$this->prefixlist_bb();
		$jar=new CookieJar;
		$loginurl='https://bg168.bos138.com/hex/login';
		$header=array(
			'Referer'=>'https://bg168.bos138.com/vi/login',
			'Content-Type'=>'application/json;charset=UTF-8',
		);
		$post=array(
			'username'  => 'bgbot',
            'password'    => '123qweyy',
		);
		$token=$this->bodytool($loginurl,$header,'POST',$jar,json_encode($post),'body');
		$token=json_decode($token,1);
		$session_id=$token['data']['session_id'];
		$cookie="sid=$session_id";
		$result_total=array();
		


		if($game==8){		#撈取彩金 grand、福、祿、壽、喜
			$page=1;
			$switch=[1,9,10,11,12];
			$jkdata=[];
			for($i=0;$i<=count($switch)-1;$i++){

				$callback=function($page,$jkdata)use(&$callback,$cookie,$s_date,$e_date,$i,$switch){
					$tmp_jkdata=$jkdata;
					$havedata=false;
					$jkurl='https://bg168.bos138.com/game/jp_history/jphistorylist';
					$header=array(
						'Cookie'=>$cookie,
						'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36',
					);
					$jkpost=array(
						'infostartdate'=>$s_date,
						'infoenddate'=>$e_date,
						'jptype'=>$switch[$i],
						'jphallid'=>'3820193',
						'gametype'=>'0',
						'page' =>$page,
						'perpage'=> '1000',
					);
					$contents=$this->bodytool($jkurl,$header,'POST',NULL,$jkpost);
					$dom =new DOMDocument;
			        @$dom->loadHTML($contents);
			    
			        if($dom->getElementsByTagName('table')->length!=0){
			            $tby = $dom->getElementsByTagName('table')->item(0)->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
			            foreach ($tby as $key => $value) {  
    		                $BB_username=$value->getElementsByTagName('td')->item(2)->nodeValue;
    		                $jk=str_replace(',', '',@$value->getElementsByTagName('td')->item(6)->nodeValue);
    		                if($BB_username != ''){
    		                	if(isset($tmp_jkdata[$BB_username])){
    		                		$tmp_jkdata[$BB_username]+=$jk;
    		                		$havedata=true;
    		                	}else{
    		                		$tmp_jkdata[$BB_username]=$jk;
    		                		$havedata=true;
    		                	}
    			        	}
			            }
			            
			        }
			        if($havedata){
			        	$page++;
			            return $callback($page,$tmp_jkdata);
			        }else{
			        	return $tmp_jkdata;
			        }
		    	};
		    	$jkdata=$callback($page,$jkdata);
			}
		}
		$page=1;
		$callback=function($page)use(&$callback,$cookie,$s_date,$e_date,&$return_total,$allprefix,$gameid){

			$header=array(
				'Cookie'=>$cookie,
				'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.70 Safari/537.36',
			);
			$reurl='https://bg168.bos138.com/report/search_result/gamekind_report';
			$repost=array(
				'SearchForm[GameKind]'=>$gameid,
				'SearchForm[GameType]'=>'0',
				'SearchForm[WagersSearch]'=>'Total',
				'SearchForm[Level]'=>'5',
				'SearchForm[Currency]'=>'ALL',
				'SearchForm[StartDate]'=>$s_date,
				'SearchForm[EndDate]'=>$e_date,
				'SearchForm[ReportLevel]'=>'1',
				'SearchForm[HallChoose]'=>'3820193',
				'SearchForm[UserID]'=>'3820193',
				'SearchForm[Page]'=>$page,
				'SearchForm[Order]'=>'',
				'SearchForm[SortColumn]'=>'',
				'SearchForm[SearchUser]'=>'',
				'SearchForm[AllHallCondition]'=>'all',
				'SearchForm[IncludeTest]'=>'0',
				'SearchForm[IsSCReport]'=>'false',
				'SearchForm[SCReportOwn]'=>'false',
				'SearchForm[SimpleReport]'=>'false',
				'SearchForm[GameKindReport]'=>'false',
				'SearchForm[Month]'=>'0',
			);
			$report=$this->bodytool($reurl,$header,'GET',null,null,null,$repost);
			$report=json_decode($report,1);
			if($report['data']['data']['Report']['Data']!=null){
				foreach ($report['data']['data']['Report']['Data'] as $k => $v) {
					$payout=$v['AGProfit']['Origin'];
					$prefix=$v['LoginName']['Origin'];
					$total[$prefix]=[
						'payout'=>$payout,
					];

				}
				foreach ($allprefix as $k => $v) {
					if(isset($total[$v])){
						$return_total[$k]=[
							'prefix'=>$k,
							'payout'=>$total[$v]['payout'],
						];
					}
				}
				$page++;
				return $callback($page);

			}else{

				return $return_total;

			}

			
		};
		$result_total=$callback($page);
		if($game==8){
			$eprefix=array('bet','yhh','b35','xab','yh6','ver','hg8','tgo','xtd','ccc','vag');//前綴+e的站台
			$aprefix=array('lis');
			foreach ($allprefix as $k => $v) {

				if(in_array($k,$eprefix)){
					$newk='e'.$k;
					foreach ($jkdata as $s => $t) {
						if(preg_match("/^$newk/",$s)){
							$result_total[$k]['payout']-=$t;
						}
					}
				}else if(in_array($k,$aprefix)){
					$newk='a'.$k;
					foreach ($jkdata as $s => $t) {
						if(preg_match("/^$newk/",$s)){
							$result_total[$k]['payout']-=$t;
						}
					}
				}else{
					foreach ($jkdata as $s => $t) {
						if(preg_match("/^$k/",$s)){
							$result_total[$k]['payout']-=$t;
						}
					}
				}
				
			}
		}
		//$result_total=json_encode($result_total);
		return $result_total;

	}
}