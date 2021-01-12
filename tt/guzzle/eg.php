<?php
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php'; 

class eg extends tools{

	public function index($s_date,$e_date,$game=null){
		$jar=new CookieJar;
		$loginurl='https://eeb2x.e-gaming.mobi/api/login';
		$header=[];
		$post=[
			'name'=> 'BOSCS',
			'password'=> 'bKGXXPSXurfc',
			'code'=> '',
			'language'=> 'zh_cn',
		];
		$re=$this->bodytool($loginurl,$header,'POST',$jar,$post);
		$re=json_decode($re,1);
		$token=$re['token'];
		$reurl = "https://eeb2x.e-gaming.mobi/api/report/tool_report?channel_id=201&currency_id=0&start=$s_date&end=$e_date&timezone=28800000";
		$header2=[
			'authorization'=>$token,
			'refer'=>'https://eeb2x.e-gaming.mobi/',
		];
		$res=$this->bodytool($reurl,$header2,'GET',$jar);
		$res=json_decode($res,1);
		foreach ($res['list'] as $k => $v) {
			preg_match('/bos(.+)/', $v['channel_name'],$prefix);
			$payout=$v['total_surplus']/100+$v['jackpot_surplus']/100-$v['jackpot_out']/100;
			//$bet=$v['valid_bet']/100; //月底產報表用
			if(isset($total[$prefix[1]])){
				$total[$prefix[1]]['payout']+=$payout;
				$total[$prefix[1]]['bet']+=$bet;
			}else{
				$total[$prefix[1]]=[
					'prefix'=>$prefix[1],
					//'bet'=>$bet,//月底產報表用
					'payout'=>$payout,
				];
			}
		}
		// foreach ($total as $k => $v) {
		// 	print_r($v['prefix']."\t".$v['bet']."\t".$v['payout'].PHP_EOL);
		// }
		//exit; //月底產報表用
		return $total;
	}
}