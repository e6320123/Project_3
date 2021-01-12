<?php
// require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class cq9 extends tools{

	public function index($s_date,$e_date,$game=null){

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


		#登入
		$jar =new CookieJar;
		$post=[

			'type'=>'SS',
			'username'=>'bos_cs',
			'password'=>'Meq0q9jYDvMM',

		];
		$loginurl='https://booa.cqgame.cc/api/login';
		$headers=array(

			'accept'=>'application/json, text/plain, */*',
			'user-agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',

		);
		$body=$this->bodytool($loginurl,$headers,'POST',$jar,$post);
		$user=json_decode($body);
		$usertoken=$user->data->usertoken;


		#报表
		$headers2=array(

					'accept'=>'application/json, text/plain, */*',
					'user-agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',				
					'usertoken'=>$usertoken,

		);

		$nowpage=1;
		$recursive=function($nowpage) use (&$return_data,&$recursive,$client,$usertoken,$enddate,$startdate,$headers2,$jar){
			//print_r($data['page']);exit;
			try{
				
				$reporturl='https://booa.cqgame.cc/api/report/finance?parentAcc=&parentid=&startDate='.$startdate['year'].'%2F'.$startdate['month'].'%2F'.$startdate['day'].'&endDate='.$enddate['year'].'%2F'.$enddate['month'].'%2F'.$enddate['day'].'&groupby=day&timezone=8&currency=CNY&nowpage='.$nowpage.'&pagesize=1000&isFetching=false';
				$body=$this->bodytool($reporturl,$headers2,'GET',$jar,null);
				$page=json_decode($body);
				// print_r($page);exit;
				foreach($page->data->data as $k=> $d){

					$prefix=$d->parentAcc;
					$payout=$d->parentincome;
					($prefix=='bosjsh') ? $prefix='bosjss' : false ;
					($prefix=='bos_svip') ? $prefix='bossvip' : false ;
					($prefix=='bos_123') ? $prefix='bos123' : false ;
					if($payout!=0){
						preg_match('/bos(.+)/',$prefix,$prefix);
						$tmp_data=[

							'prefix'=>$prefix[1],
							'payout'=>$payout,

						];
						$return_data[]=$tmp_data;
					}
				}
				$nowpage++;
				//print_r($nowpage);
				if(isset($tmp_data)){

					return $recursive($nowpage);

				}

			} catch (\Exception $e) {

		        print_r($e->getmessage());
		        exit;
	      	}

	      	return $return_data;

		};
		
		$total=$recursive($nowpage);
		foreach($total as $v) {						#先用站台名称作索引把重复站的帐务相加，再用array_value()把索引变成数字

		    if(isset($su[$v['prefix']])) {

				$su[$v['prefix']]['payout'] += $v['payout'];
				
			}
			else{
				$su[$v['prefix']]=$v;
			}
			
		}
		$result=array_values($su);
		array_multisort($su, SORT_ASC, $result); 
		return $result;

	}
}

// $x=new cq9;
// $x->index('2019-04-01','2019-04-30');