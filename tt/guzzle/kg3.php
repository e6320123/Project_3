
<?php
// require_once '../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class kg3 extends tools{

	public function index($s_date,$e_date,$game=null){

		//login page
		$jar=new CookieJar;
		$loginurl='https://www3.v33kgg.com/agent/web/index.php?r=site%2Flogin';
		$header=array(

			'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',

		);
		$logbody=$this->bodytool($loginurl,$header,'GET',$jar,null);

		$dom=new DOMDocument();
        @$dom->loadHTML($logbody);
        $csrf_agent=$dom->getElementById("login-form")->getElementsByTagName("input")->item(0)->attributes->item(2)->nodeValue;
        // print_r($csrf_agent);
        // exit;
        //login url
        $reloginurl = "https://www3.v33kgg.com/agent/web/index.php?r=site%2Flogin";

		$post=array(

			'_csrf-agent'=> $csrf_agent,
		    'LoginForm[username]'=>'bos',
		    'LoginForm[password]'=>'rCn35XSc',
		    'login-button'=>'',

		);
		$logbody=$this->bodytool($reloginurl,$header,'POST',$jar,$post);

		$reloginurl = "https://www3.v33kgg.com/agent/web/index.php?r=merchant-winlose%2Findex";

		$query = array(
			'r' => 'merchant-winlose/index',
		);

		$logbody=$this->bodytool($reloginurl,$header,'GET',$jar,null,$query);

		$reloginurl = "https://www3.v33kgg.com/agent/web/index.php?r=merchant-winlose%2Findex&time_range=".$s_date."+00%3A00%3A00+-+".$e_date."+23%3A59%3A59&start_time=".$s_date."+00%3A00%3A00&end_time=".$e_date."+23%3A59%3A59&source=all&game%5B%5D=1&game%5B%5D=2&game%5B%5D=3&game%5B%5D=4&game%5B%5D=5&game%5B%5D=6";

		$query = array(
			'r' => 'merchant-winlose/index',
			'time_range' => '2019-11-19 00:00:00 - 2019-11-19 23:59:59',
			'start_time' => '2019-11-19 00:00:00',
			'end_time' => '2019-11-19 23:59:59',
			'source' => 'all',
			'game[]' => '1',
			'game[]' => '2',
			'game[]' => '3',
			'game[]' => '4',
			'game[]' => '5',
			'game[]' => '6',

		);

		$logbody=$this->bodytool($reloginurl,$header,'GET',$jar,null,$query);

		$dom=new DOMDocument();
		@$dom->loadHTML($logbody);
		$tables = $dom->getElementsByTagName('table');
		$rows = $tables->item(0)->getElementsByTagName('tr');

		foreach ($rows as $s => $row)
		{
			$prefix= $row->getElementsByTagName('td')->item(0)->nodeValue;
			$payout= $row->getElementsByTagName('td')->item(4)->nodeValue;
			$prefix = explode('-',$prefix);
			if($payout!=0 && $prefix[1]!=''){
			    $total[]=[
			    	'prefix'=>$prefix[1],
			    	'payout'=>$payout,
			    ];
			}
		}
		foreach($total as $b){

          $na[]=$b['prefix'];

        }
		array_multisort($na, SORT_ASC, $total);

		//print_r($total);
		return $total;
	}
}

// $x=new kg3;
// $x->index('2019-11-19','2019-11-19');

