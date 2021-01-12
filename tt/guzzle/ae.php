
<?php
// require_once '/var/www/html/vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class ae extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;		
		$loginurl='https://sso.fafafa3388.com/login';
		$header=array(

			'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',

		);
		$logbody=$this->bodytool($loginurl,$header,'GET',$jar,null);
		$dom=new DOMDocument();
        @$dom->loadHTML($logbody);                             
        $lt=$dom->getElementById("lt")->getAttribute('value');
        $authenticity_token=$dom->getElementById("login-form")->getElementsByTagName("input")->item(1)->attributes->item(2)->nodeValue;
        $token=array(    

            $lt,
            $authenticity_token,

        );

        $vurl='https://sso.fafafa3388.com/rucaptcha/';
		$pic=$this->bodytool($vurl,null,'GET',$jar,null);
		$i=imagecreatefromstring($pic);
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
		$post=array(

			'utf8'=>'✓',
			'authenticity_token'=>$token[1],
			'lt'=>$token[0],
			'service'=>'https://backoffice.fafafa3388.com/?locale=enUS',
			'username'=>'boscs',
			'password'=>'1S1UBiq28a1',
			'_rucaptcha'=>$vcode,
			'button'=>'',

		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);

		$e=new DateTime($e_date);
		$ed_date=$e->modify('+1 day')->format("Y-m-d");	

		$page=1;
		$recursive=function($page) use (&$return_data,&$recursive,$client,$header,$ed_date,$s_date,$jar){

			try{
				//print_r($page);
				$reurl='https://hsp.fafafa3388.com/winlose_report/index?end_date='.$ed_date.'+00%3A00+%2B8&locale=zhTW&login_name=&page='.$page.'&player_type=normal&site_id=all&start_date='.$s_date.'+00%3A00+%2B8';
				$body=$this->bodytool($reurl,$header,'GET',$jar,null);
				$body = html_entity_decode($body);
				//preg_match_all('/:\S+/',$body,$matches);
				preg_match_all('/(?<=:records=\x27)(.*)(?=\x27)/',$body,$matches);
				$a = $matches[0][0];
				$a = json_decode($a,true);
				foreach ($a as $key => $value) 
				{
					$total[$key]['prefix'] = $value['site'];
					$total[$key]['payout'] = $value['net'];

					if($total[$key]['prefix']=='bosjsh')
					{
		        		$total[$key]['prefix']='bosjss';
		        	}
		        	if($total[$key]['prefix']=='bosn888')
		        	{
		        		$total[$key]['prefix']='bosn88';
		        	}
					preg_match('/bos(.+)/',$total[$key]['prefix'],$prefix);
					if ($total[$key]['payout'] != 0)
					{
						$tmp_data[] = [
							'prefix'=>$prefix[1],
							'payout'=>$total[$key]['payout'],
						];
					}

					
		   		}
		   		if ($tmp_data != null) 
		   		{
		   			foreach($tmp_data as $t)
					{

		   				$return_data[]=$t;
		   			}
		   		}
       			$page++;

       			if(isset($tmp_data)){
       				
					return $recursive($page);

				}

			}	catch (\Exception $e) {

		        print_r($e->getmessage());
		        exit;
	      	}
	      	return $return_data;

		};

		$total=$recursive($page);
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);     
        return $total;

			

	}
}

// $x=new ae;
// $x->index();

