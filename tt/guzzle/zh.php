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

class zh extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;
		$loginurl='https://platform.zhqp99.com/login';
		$header=array(
			'Accept'=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			'Accept-Encoding'=> 'gzip, deflate',
			'Accept-Language'=> 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			//'User-Agent'=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36'
		);
		$b=$this->bodytool($loginurl,$header,'GET',$jar);
		$dom=new DOMDocument;
		$dom->loadHTML('<?html encoding="utf-8" ?>'.$b);
		$token = $dom->getElementsByTagName('input')->item(0)->getAttribute('value');
		$post=array(	
			'_token' => $token,
			'username' => 'BOS_boscs_cs',
			'password' => 'boscs123'
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);
		$reurl='https://platform.zhqp99.com/report/win-loss/merchant';
		$query=array(
			'start_date' => $s_date,
			'end_date' => $e_date,
			'code_MerchantMaster' => '0',
			'code_GameMaster' => '0'
		);
		$report=$this->bodytool($reurl,$header,'GET',$jar,null,null,$query);
		$dom=new DOMDocument;
		@$dom->loadHTML('<?html encoding="utf-8" ?>'.$report);
		$tbody = $dom->getElementsByTagName('tbody')->item(0);
		$trs = $tbody->getElementsByTagName('tr');
		foreach($trs AS $k=>$tr){
		    $prefix = $tr->getElementsByTagName('td')->item(1)->nodeValue;
		    $prefix=str_replace('bos','',$prefix);
		    ($prefix=='368')?$prefix='3368':false;
		    ($prefix=='xpj')?$prefix='axpj':false;
		    ($prefix=='sgbt')?$prefix='gbt':false;
		    $payout = $tr->getElementsByTagName('td')->item(8)->nodeValue;
		    //$money = str_replace(',','',$money);
		    $total[$k]=[
		    	'prefix'=>$prefix,
		    	'payout'=>$payout,
		    ];

		}
		return $total;
	}

}
// $x=new zh;
// $x->index('2019-08-01','2019-08-02');