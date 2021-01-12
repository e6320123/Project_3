<?php
//require_once '/var/www/html/vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class sg extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;	
		$vurl='https://backoffice.imspade.com/validationCode';
		$pic=$this->bodytool($vurl,null,'GET',$jar,null);

			$i=imagecreatefromstring($pic);
			for ($y=0;$y<imagesy($i);$y++) {          

	    		for ($x=0;$x<imagesx($i);$x++) {

	        		$rgb = imagecolorat($i,$x,$y);
	        		$r = ($rgb>>16) & 0xFF;
	        		$g = ($rgb>>8) & 0xFF;
	        		$b = $rgb & 0xFF;
	    		    if($b < '100' ){          

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

		$loginurl='https://backoffice.imspade.com/login.action';
		$header=array(

			'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',	
			'accept-language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',			
			'user-agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
			
		);
		$post=array(

			'merchant'=>'BOS',
			'userId'=>'BOSSUP',
			'passwd' => '0ec3cd548fa93cc98adb2254ae89ee7df713531a24cd7110014e5a2be91bc4db751c37123194b47366f8c09169c86410b619c4ec56dd8c6ba840b6f08ec05967760a3e2999b2f8964d31cde24dddca6ea70334b8ce67cb95f9ff2918c82792425932950f4e1a9bb2759aeabc4c92a8c667e6ba6bd1a45c30eded260602aa57b2',
			'randomCode'=>$vcode,

		);

		$body=$this->bodytool($loginurl,$header,'POST',$jar,$post);

		$reurl='https://backoffice.imspade.com/report/profitLoss.action';
		$post2=array(

			'finished'=>'true',
			'req.beginDate'=>$s_date,
			'req.beginTime'=>'00:00',
			'req.endDate'=>$e_date,
			'req.endTime'=>'23:59',
			'timmer'=>'false',

		);
		$body2=$this->bodytool($reurl,$header,'POST',$jar,$post2);
		$dom =new DOMDocument();                       
        @$dom->loadHTML($body2);                     
        $trs=$dom->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');
        foreach ($trs as $k => $v) {                    
        
            $prefix=trim($v->getElementsByTagName('td')->item(0)->nodeValue);
            $payout=trim($v->getElementsByTagName('td')->item(6)->nodeValue);

            if(preg_match("/bos/i",$prefix) && $payout!=0){ 
            	preg_match("/bos(.+)/i",$prefix,$prefix);

              	$total[$k]=[

                     'prefix' => $prefix[1],
                     'payout' => $payout,

               ];
            }                 
        } 
        foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);      
        return $total;
		
	}
}
// $x=new sg;
// $x->index();