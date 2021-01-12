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
require_once 'class.tools.php';

class leg extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date('Y-m-d', strtotime($e_date));

		//登入
		$jar=new CookieJar;
		$loginurl='https://hout.loyo1.com/login';
		$header=array(
		);
		$post=array(

			'name'=> 'boscs',
            'password'=> md5('321cba###'),
            'validateCode'=> '',
            'count'=> '0',

		);

		$logresult=function()use(&$logresult,$loginurl,$header,$jar,$post){
			$indexurl='https://hout.loyo1.com/default';
			$this->bodytool($loginurl,$header,'POST',$jar,$post);
			$code=$this->bodytool($indexurl,$header,'GET',$jar,null);
			preg_match('/代理商后台/',$code,$code2);
			if($code2==null) {
				sleep(3);
				return $logresult();
			}else{
				return true;
			} 
			
		};

		if($logresult()){
		
			$getdata=function()use($header,&$jar,&$getdata,$s_date,$e_date){
									
					//取得輸贏資料
				$get_data_url='https://hout.loyo1.com/proxyStatis/initData?beginDate='.$s_date.'&endDate='.$e_date.'&proxyName=&channelId=70181&limit=500&offset=0&total=0&gameid=&GameName=%E5%85%A8%E9%83%A8&searchType=&_='.time().rand(100,999);
				$getre2=$this->bodytool($get_data_url,$header,'GET',$jar, null);
				$getre2 = json_decode($getre2, true);
				if($getre2==null){
					echo '重抓'.PHP_EOL;
					sleep(3);
					return $getdata();
				}else{
					return $getre2;
				}
			};
			$tmp_return=$getdata();
			foreach($tmp_return['rows'] as $k => $v){
				if(strtolower(substr($v['ChannelName'],3)) == 'n888'){
					$prefix = 'n88';
					$payout=$v['Profit']/100;
					if($payout!=0){

						$total[$k]=[
							'prefix'=>$prefix,
							'payout'=>$payout,
						];

					}
				}
				else{
					$prefix = strtolower(substr($v['ChannelName'],3));
					$payout=$v['Profit']/100;
					if($payout!=0){

						$total[$k]=[
							'prefix'=>$prefix,
							'payout'=>$payout,
						];

					}
				}
				
				
			}
			foreach($total as $b){  

				$na[]=$b['prefix'];

			}
			array_multisort($na, SORT_ASC, $total);
			return $total;
		}

	}

}

// $x=new leg;
// $x->index('2019-07-01','2019-07-02');