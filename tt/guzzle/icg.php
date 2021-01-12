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

class icg extends tools{

	public function index($s_date,$e_date,$game=null){
		$jar=new CookieJar;
		$loginurl='https://admin.iconic-gaming.com/service/login';
		$header=[
			'content-type'=>'application/json;charset=UTF-8',
		];
		$post=[
			'password'=>'jTBEnsMWSS2a',
			'username'=>'bossup',
		];
		$login=$this->bodytool($loginurl,$header,'POST',$jar,$post,'json');
		$login=json_decode($login,1);
		$token=$login['token'];
		$reurl='https://admin.iconic-gaming.com/service/api/v1/profile/platformStats?';
		$header=[
			'authorization'=>'Bearer '.$token,
		];
		$post=[
			'start'=>strtotime($s_date.' 00:00:00').'000',
			'end'=>strtotime($e_date.' 23:59:59').'000',
			'page'=>'1',
			'rowsPerPage'=>'1000',
			'sortBy'=>'name',
			'descending'=>'false',
		];
		$re=$this->bodytool($reurl.http_build_query($post),$header,'GET',$jar);
		$re=json_decode($re,1);
		foreach ($re['data'] as $k => $v) {
			preg_match('/bos(.+)/', $v['name'],$prefix);
			$payout=($v['win']-$v['bet'])*0.01;
			$total[]=[
				'prefix'=>$prefix[1],
				'payout'=>$payout,
			];
		}
		return $total;
	}
}