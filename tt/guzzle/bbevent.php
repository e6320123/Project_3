<?php 
//require_once('../../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
// require_once('../sql/sqldata.php');
require_once 'class.tools.php';

class bbevent extends tools{

	public function index($s_date,$e_date,$game=null){



		$transprefix=array('ebet','eyhh','eb35','alis','exab','eyh6','ever','ehg8','etgo','extd','eccc','evag');

		echo '预设都是从开始日其抓七天';
		$url='http://linkapi.bge888.com/app/WebService/JSON/display.php/GetPromotionWinList';

		$startdate=date_parse_from_format('Y-m-d', $s_date);
		$enddate=date_parse_from_format('Y-m-d', $e_date);
		$day=$startdate['day'];

		$round=1;
		$recursive=function($round)use(&$day,&$return_data,$url,&$recursive){
			try{

				$post=array(
					'website'=>'bgenter',
					'rounddate'=>'2019-09-'.$day,
					'starttime'=>'00:00:00',
					'endtime'=>'23:59:59',
					'key'=>'1234567894486561b4ddf953989568a2ea5f204c312345',
				);
				$header=array();
				$data=$this->bodytool($url,$header,'POST',null,$post);

				$data=json_decode($data,true);
				foreach ($data['data'] as $k => $v) {

					// if(isset($return_data[$v['UserName']])){
					// 	$return_data[$v['UserName']]['payout']+=$v['Amount'];
					// }else{
						$return_data[]=[
							'user'=>$v['UserName'],
							'payout'=>$v['Amount'],
						];
					//}
				}
				$round++;
				$day++;
				if($round<=7){
					return $recursive($round);
				}


			}catch(\Exception $e){
				print_r($e->getmessage());
		        exit;
			}
			
			return $return_data;

		};

		$total=$recursive($round);
		//print_r($total);exit;
		foreach ($total as $k => $v) {
			if(isset($res[$v['user']])){
				$res[$v['user']]['payout']+=$v['payout'];
			}else{
				$res[$v['user']]=[
					'user'=>$v['user'],
					'payout'=>$v['payout'],
				];
			}
		}


		$sql=new sqldata;
		$prefixlist=$this->prefixlist();

		foreach ($prefixlist as $k => $v) {

			foreach ($res as $s => $t) {

				//print_r(substr($t['user'],0,4).PHP_EOL);
				if(in_array(substr($t['user'],0,4),$transprefix)){
					$t['user']=substr($t['user'],1);

				}
				if(preg_match("/$v/",substr($t['user'],0,strlen($v)))){

					if(isset($result[$v])){

						$result[$v]['payout']+=$t['payout'];

					}else{
						$result[$v]=[
							'prefix'=>$v,
							'payout'=>$t['payout'],
						];

					}
					
				}
					
			}

		}
		return $result;


	}

}

// $x=new bbevent;
// $x->index('2019-08-14','2019-08-21');