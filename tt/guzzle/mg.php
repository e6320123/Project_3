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
require_once 'class.tools.php';

class mg extends tools{

	public function index($s_date,$e_date,$game=null){


		$MG2_account ='BOSReadOnly';
		$MG2_password = '7wPO6X87FlFy5Mz4UU5x';
		$jar=new CookieJar;
		$loginurl='https://ui.adminserv88.com/oauth/token';
		$header=array(
            'Authorization' => 'Basic '. base64_encode($MG2_account.':'.$MG2_password),             
			'Cookie' => '__cfduid=d4240e6e5629db95456ce4ad22407e9a01550547021',
		);
		$post=array(
            'client_id'   =>  'systemui',
            'grant_type'  =>  'password',
            'username'    =>  $MG2_account,
            'password'    =>  $MG2_password,
		);
		$body=$this->bodytool($loginurl,$header,'POST',$jar,$post);
		$token = json_decode($body)->access_token;
		$MG2token = 'Bearer '.$token;

		$reurl='https://api.adminserv88.com/v1/report/656297?start_time='.$s_date.'&end_time='.$e_date.'&page=1&page_size=500&include_test=false&dimensions=downline_id&metrics=unique_member%2Cnr_tx_sum%2Cnr_tx%3Awager_sum%2Csub%5Bamount%3Awager_sum%7Camount%3Arefund_sum%3Acredit%5D%2Csub%5Bamount%3Apayout_sum%7Camount%3Arefund_sum%3Adebit%5D%2Cgross_revenue_sum%2Cdiv%5Bgross_revenue_sum%7Csub%5Bamount%3Awager_sum%7Camount%3Arefund_sum%3Acredit%5D%5D';
		$header2=array(
			'Authorization' =>  $MG2token,
			'X-DAS-CURRENCY' => 'CNY',
			'X-DAS-TZ' => 'UTC-04:00',
			'X-DAS-LANG' => 'zh-CN',
			'X-DAS-TX-ID' => 'ec718086-2aef-4038-b82e-acec81845464'
		);
		$report=$this->bodytool($reurl,$header2,'GET',$jar);
		$report = json_decode($report,true);
		//print_r($report);exit;
		


		foreach ($report['data']['rows'] as $key => $value) {
		    	//print_r($value);
		        $tmp_data[$value[7]]=[

		        	'payout'=>$value[5]
		        ];
		    
		}
		
		foreach($tmp_data as $k =>$v){

			$idurl='https://api.adminserv88.com/v1/accounts/'.$k;
			$header3=array(
				'Authorization' =>  $MG2token,
				'X-DAS-CURRENCY' => 'CNY',
				'X-DAS-TZ' => 'UTC-04:00',
				'X-DAS-LANG' => 'zh-CN',
				'X-DAS-TX-ID' => 'c443361f-325f-492e-8e43-8e2029e165ee'
			);
			$idlist=$this->bodytool($idurl,$header3,'GET',$jar);
			$idlist=json_decode($idlist,true);
			$prefix=$idlist['data'][0]['name'];
			preg_match('/BOS(.+)/',$prefix,$newprefix);
			$payout=$v['payout'];
			$total[$prefix]=[
				'prefix'=>$newprefix[1],
				'payout'=>$payout,
			];
		}
		//print_r($total);
		return $total;

	}

}
// $x=new mg;
// $x->index('2019-08-01','2019-08-02');