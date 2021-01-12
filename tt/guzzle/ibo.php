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
require_once 'class.tools.php';


class ibo extends tools{

	public function index($s_date,$e_date,$game=null){



		$loging_url='http://sag.iabc365.com/app/logins.php?langx=zh-tw&kind=s';
		$header=array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36',
			'Upgrade-Insecure-Requests' => '1',
		);
		$login_post=array(
			'active'=>'1',
			'langx'=>'zh-tw',
			'radiobutton'=>'radiobutton',
			'username'=>'boscs001',
			'pswd'=>'123qwe',
			'number'=>'6519',
			'Submit.x'=>'84',
			'Submit.y'=>'32',
		);
		$login=$this->bodytool($loging_url,$header,'POST',null,$login_post);
		preg_match("/(?<=uid=')(.*)(?=';)/i",$login,$uid);

		$reurl='http://sag.iabc365.com/app/report_new2/report_suagent.php';
		$query=array(

			'langx'=>'zh-tw',
			'uid' => $uid[0],
			'ltype'=>'s',
			'table'=>'su_agents',
			'lid'=>'320',
			'viewself'=>'N',
			'gtype'=>'',
			'date_start'=>$s_date,
			'date_end'=>$e_date,
			'report_kind'=>'A',
			'tbid'=>'',
			'rtype'=>'',
			'nc_rtype'=>'',
			'rp'=>'',
			'cur_id'=>'RMB',
			
		);
		
		$report=$this->bodytool($reurl,$header,'GET',null,null,null,$query);
		$dom = new domDocument();
		@$dom->loadHTML($report);
		$tables = $dom->getElementsByTagName('table');
		$rows = $tables->item(1)->getElementsByTagName('tr');
		foreach ($rows as $k =>$row) {

	    	$prefix= $row->getElementsByTagName('td')->item(1)->nodeValue;
	      	$payout= $row->getElementsByTagName('td')->item(11)->nodeValue;
	      	preg_match('/bos(.+)/i',$prefix,$prefix);
	      	if($payout!=0){
			    $total[$k]=[
			    	'prefix'=>$prefix[1],
			    	'payout'=>$payout,
			    ];
			}
	    
		}
		//print_r($total);
		return $total;
		
	}
}

// $x=new ibo();
// $x->index('2019-09-09','2019-09-09');