<?php   
//require_once 'vendor/autoload.php';
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php'; 

class kg extends tools{

	public function index($s_date,$e_date,$game=null){
		$jar = new CookieJar();
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


		$vurl='http://www2.v88kgg.com/vendor/file_manager/scripts/verify.php';
		$header=array(

			'Accept'=>'image/webp,image/apng,image/*,*/*;q=0.8',
			'Accept-Encoding'=>'gzip, deflate',
			'Accept-Language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
			'Host'=>'www2.v88kgg.com',
			'Referer'=>'http://www2.v88kgg.com/vendor/login.php',
			'Connection'=>'keep-alive',

		);
		$body=$this->bodytool($vurl,$header,'GET',$jar,null);
		file_put_contents('image.gif', $body);
		$i=imagecreatefromstring($body);
		for ($y=0;$y<imagesy($i);$y++) {          

    		for ($x=0;$x<imagesx($i);$x++) {

        		$rgb = imagecolorat($i,$x,$y);
        		$r = ($rgb>>16) & 0xFF;
        		$g = ($rgb>>8) & 0xFF;
        		$b = $rgb & 0xFF;
    		    if($b < '5' ){          

                    echo'*';

                }
                else{

                    echo' ';
                }

            }

            echo "\n";            
        }
		fwrite(STDOUT, "验证码:");
		$vcode = trim(fgets(STDIN));
		$loginurl='http://www2.v88kgg.com/vendor/file_manager/ajax/post/login.php';
		$post=array(

			'LoginName'=>'bg',
			'LoginPassword'=>'1234DCBA',
			'VerifyCode'=>$vcode,
			'Language'=>'sc',
			'LoginType'=>'2',
			'IPAddress'=>'211.23.3.66',

		);
		$header2=array(

			'Accept'=>'*/*',
			'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.131 Safari/537.36',
		);
		$this->bodytool($loginurl,$header2,'POST',$jar,$post);

		$prefixdataurl='http://www2.v88kgg.com/vendor/file_manager/ajax/total_report_winloss.php';
		$pdata=$this->bodytool($prefixdataurl,$header2,'GET',$jar,null);
		$dom =new DOMDocument();                       
		@$dom->loadHTML($pdata);                     
		$xpath = new DOMXPath($dom);
		$nodeList=$xpath->query('//*[contains(@name,"VendorId")]/option');
		foreach ($nodeList as $n) {

		    $prefixdata[]= [

		    	'id'=>$n->attributes->getNamedItem('value')->nodeValue,		#站台代码
		    	'name'=>$n->nodeValue,											#站台名称

			];
		}

		foreach($prefixdata as $k=>$v){

			$dataurl='http://www2.v88kgg.com/vendor/file_manager/ajax/total_report_winloss.php?VendorId='.$v['id'].'&StartTime='.$startdate['year'].'%2F'.$startdate['month'].'%2F'.$startdate['day'].'&EndTime='.$enddate['year'].'%2F'.$enddate['month'].'%2F'.$enddate['day'].'&&Page=1';
			$data=$this->bodytool($dataurl,$header2,'GET',$jar,null);
			$dom =new DOMDocument();                       
			@$dom->loadHTML($data);                     
			$xpath = new DOMXPath($dom);
			$nodeList2=$xpath->query('//*[contains(@class,"ignore num")]');
			$payout=$nodeList2->item(0)->nodeValue;
			if(preg_match("/BOS/i",$v['name']) && $payout!=0){
				preg_match("/bos_(.+)-/i",$v['name'],$prefix);
				$total[$k]=[
					'prefix'=>$prefix[1],
					'payout'=>$payout,
				];
			}	
		}

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);      
		return $total;
		//print_r($total);
	}
}
// $x=new kg;
// $x->index();