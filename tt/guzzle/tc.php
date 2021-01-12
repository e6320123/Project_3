<?php
// require_once('../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class tc extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;
		$url='http://connect2bo.com/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FTCG%2Flott%2Fperformance&reportUnit=%2Freports%2FTCG%2Flott%2Fperformance%2FmerchantLottoPerformanceReport&standAlone=true&j_username=readonly&j_password=readonly&decorate=no&operator_name=BGE_CAIWU3&userLocale=zh_CN';
		$header=array(
			'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
		);
		$report=$this->bodytool($url,$header,'GET',$jar);
		preg_match("/__.flowExecutionKey = \"(.+)\"/",$report,$flowkey);
		$url='http://connect2bo.com/jasperserver/JavaScriptServlet';
		$header=array(
			'Accept'=>'*/*',
			'Accept-Encoding'=>'gzip, deflate',
			'Accept-Language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'FETCH-CSRF-TOKEN'=>'1',
			'Host'=>'connect2bo.com',
			'Origin'=>'http://connect2bo.com',
			'X-Requested-With'=>'XMLHttpRequest',
			'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
		);
		$post=array();
		$token=$this->bodytool($url,$header,'POST',$jar,$post);
		preg_match('/OWASP_CSRFTOKEN:(.+)/',$token,$token);


			
		$getdata=function()use($s_date,$e_date,$flowkey,$token,&$getdata,$jar){

			$reurl="http://connect2bo.com/jasperserver/flow.html?_flowExecutionKey=".$flowkey[1]."&_flowId=viewReportFlow&_eventId=refreshReport&pageIndex=0&decorate=no&confirm=true&decorator=empty&ajax=true";
			$header=array(
				'Accept'=>'text/html, */*; q=0.01',
				'OWASP_CSRFTOKEN'=>$token[1],
				'Referer'=>'http://connect2bo.com/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FTCG%2Flott%2Fperformance&reportUnit=%2Freports%2FTCG%2Flott%2Fperformance%2FmerchantLottoPerformanceReport&standAlone=true&j_username=readonly&j_password=readonly&decorate=no&operator_name=BGE_CAIWU3&userLocale=zh_CN',
				'X-Requested-With'=>'XMLHttpRequest, OWASP CSRFGuard Project',
				'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
			);
			$post=array(
				'operator_name'=>'BGE_CAIWU3',
				'start_date'=>$s_date,
				'end_date'=>$e_date,
				'lott_game_category'=>'LOTT',
				'lott_game_code'=>'~NOTHING~',
				'lott_video'=>'~NOTHING~',
				'lott_device'=>'~NOTHING~',
				'currency_code'=>'~NOTHING~',
				'is_official'=>'~NOTHING~',
			);
			$report=$this->bodytool($reurl,$header,'POST',$jar,$post);
			if($report==null){
				return $getdata();
			}else{
				return $report;
			}
		};
		
		$dom=new DOMDocument;
		$dom->loadHTML($getdata());
		$trs=$dom->getElementsByTagName('tr');
		foreach($trs as $k =>$v){
			$prefix=$v->getElementsByTagName('span')->item(1)->nodeValue;
			($prefix=='bgejsh') ? $prefix='bgejss' : false ;
			($prefix=='bgen888') ? $prefix='bgen88' : false ;
			preg_match('/bge(.+)/',$prefix,$prefix);
			$payout=$v->getElementsByTagName('span')->item(9)->nodeValue;
			$payout=str_replace(",", "", $payout);
			$total[$prefix[1]]=[
				'prefix'=>$prefix[1],
				'payout'=>$payout,
			];
		}


		//天成小費
		$prefixlist=$this->prefixlist();
		foreach ($prefixlist as $k => $v) {
			$prefix="bge".$v;
			$jar=new CookieJar;
			$url="http://connect2bo.com/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FTCG%2Ftc_lott%2FsendGiftTc&reportUnit=%2Freports%2FTCG%2Ftc_lott%2FsendGiftTc%2FsendGiftTcReport&standAlone=true&j_username=readonly&j_password=readonly&decorate=no&channel=".$prefix."&userLocale=zh_CN";
			$header=array(
				'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
			);
			$report=$this->bodytool($url,$header,'GET',$jar);
			preg_match("/__.flowExecutionKey = \"(.+)\"/",$report,$flowkey);
			$url='http://connect2bo.com/jasperserver/JavaScriptServlet';
			$header=array(
				'Accept'=>'*/*',
				'Accept-Encoding'=>'gzip, deflate',
				'Accept-Language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
				'FETCH-CSRF-TOKEN'=>'1',
				'Host'=>'connect2bo.com',
				'Origin'=>'http://connect2bo.com',
				'X-Requested-With'=>'XMLHttpRequest',
				'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
			);
			$post=array();
			$token=$this->bodytool($url,$header,'POST',$jar,$post);
			preg_match('/OWASP_CSRFTOKEN:(.+)/',$token,$token);


				
			$getdata2=function()use($s_date,$e_date,$flowkey,$token,&$getdata,$jar,$prefix){

				$reurl="http://connect2bo.com/jasperserver/flow.html?_flowExecutionKey=".$flowkey[1]."&_flowId=viewReportFlow&_eventId=refreshReport&pageIndex=0&decorate=no&confirm=true&decorator=empty&ajax=true";
				$header=array(
					'Accept'=>'text/html, */*; q=0.01',
					'OWASP_CSRFTOKEN'=>$token[1],
					'Referer'=>'http://connect2bo.com/jasperserver/flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2FTCG%2Flott%2Fperformance&reportUnit=%2Freports%2FTCG%2Flott%2Fperformance%2FmerchantLottoPerformanceReport&standAlone=true&j_username=readonly&j_password=readonly&decorate=no&operator_name=BGE_CAIWU3&userLocale=zh_CN',
					'X-Requested-With'=>'XMLHttpRequest, OWASP CSRFGuard Project',
					'Cookie'=>'JSESSIONID=F88E1E5471EF67697FB62AAAEC6C77BF; token=bdd232c6-94c7-46c3-ae2c-7ac44cc34e1b; language=zh_CN; JSESSIONID=uDmR-ESJcdwdSHtDNliXXDOQ6OClBE8tmlkeuBYXjZIRm6H-OQDs!-270029865',
				);
				$post=array(
					'channel'=>$prefix,
					'start_date'=>$s_date,
					'end_date'=>$e_date,
				);
				$report=$this->bodytool($reurl,$header,'POST',$jar,$post);
				if($report==null){
					return $getdata2();
				}else{
					return $report;
				}
			};

			preg_match("/总计.*?<\/td>.*?<\/td>.*?<\/td>.*?span.*?>(.*?)<\/span>/s",$getdata2(),$result);
			$payout=$result[1];
			$payout=str_replace(",", "", $payout);
			if($payout!=null){
				if(isset($total[$v])){
					$total[$v]['payout']-=$payout;
				}else{
					$total[$v]=[
						'prefix'=>$v,
						'payout'=>$payout,
					];
				}
			}
		}
		return $total;

	}
}
// $x=new tc;
// $x->index('2019-02-01','2019-02-28');