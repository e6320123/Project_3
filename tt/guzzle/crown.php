<?php 
// require_once('../../vendor/autoload.php');
error_reporting(0); //不報錯誤

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

class crown extends tools{
	public $jar;
	public function index($s_date,$e_date,$game=null){



		$s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date('Y-m-d', strtotime($e_date));

        //獲取站台列表
        $plat = $this->prefixlist();
        foreach ($plat as $key => $value) {
        	$prefix[$key] = $value;
        }
		$this->jar = new CookieJar;

		//取得驗證碼
		$captcha_url = 'http://ag.boy999.com/DataPage/Vcode.ashx';
		$header = array(
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		);

		$captcha = $this->bodytool($captcha_url, $header, 'GET', $this->jar, null);

		$img=imagecreatefromstring($captcha);

		for ($y=0;$y<imagesy($img);$y++) {          

		    for ($x=0;$x<imagesx($img);$x++) {

		        $rgb = imagecolorat($img,$x,$y);
		        $r = ($rgb>>16) & 0xFF;
		        $g = ($rgb>>8) & 0xFF;
		        $b = $rgb & 0xFF;
		        if($b < 200 ){
		        	$im_data[$x][$y] = '*';          
	                //echo'*';

	            }
	            else{
	            	$im_data[$x][$y] = '-';
	               // echo'-';
	            }
		    }
		    //echo "\n";            
		}


		//判斷數字起始點
		$key_width=12; //數字寬
		$key_heigh=13; //數字長
		$key_n=156; //一個數字的總數

		$w = 0;
		$w_t = 0;
		$k = 1;
		$h_h = 0;

		for($i=4; $i<imagesx($img); $i++){ //width
		    for($j=4; $j<imagesy($img); $j++){ //height
		            if($w_t==0){
		                $word[$k]['width_s']=$i; //數字開頭位於圖片的寬
		                $word[$k]['width_e']=$i+$key_width-1;
		                $w_t = 1;
		                $w = $i;
		            }
		            if($h_h == 0){
		                $h_h = $j;
		            }
		            if($h_h >= $j){
		                $h_h = $j;
		                $word[$k]['heigh_h'] = $h_h;
		                $word[$k]['heigh_l'] = $h_h + ($key_heigh-1);
		            }
		    }
		    if($i == ($w+($key_width-1))){
		        $w_t = 0;
		        $k += 1;
		        $h_h = 0;
		    }
		}
		
		for($i = 1; $i <= count($word); $i++){
		    $word[$i]['key'] = '';
		    for($j = $word[$i]['heigh_h']; $j <= $word[$i]['heigh_l']; $j++){   
		        for($k = $word[$i]['width_s']; $k <= $word[$i]['width_e']; $k++){
		            $word[$i]['key'] = $word[$i]['key'].$im_data[$k][$j];
		        }
		    }
		}


		$key = array(
			'--********---*********---****--****-****---****-****---****-****---****-****--*****-****---****-****---****--****--****--*********----*******------*****----' => '0',
			'-*******------******--------****--------****--------****--------****--------****--------****--------****--------****-----**********--**********--**********-' => '1',
			'-*********---**********--**----****--------****--------***--------****-------****------*****-------****-------****-------**********-**********---**********-' => '2',
			'-*********----*********---*----****--------****--------***--------****-------****-------****-------****------*****-------**********--**********--**********-' => '2',
			'--********----********----*---****--------****-----******------*****-------*******-------******--------****--**----****--*********---*********----******----' => '3',
			'-----*****------******------******-----*******----***-****----***-****---***--****--***---****--***********************-------****--------****--------****--' => '4',
			'--********----********----***---------***---------*******-----********----********---------****--------****--**----***---*********---********-----******----' => '5',
			'---*******----********----****---*---****--------***-****----*********--***********-*****--****-****----***--****--****--**********---********------*****---' => '6',
			'***********-***********--------***---------***--------***---------***--------***---------***--------****--------***--------****-------****--------****------' => '7',
			'--********---****--****-****---****--***---****--****--***----*******-----*******---*****--***--****----***-****----***-****---****--*********-----******---' => '8',
			'--********---*********--****---****-****---****-****---****-***********-***********---*********--------****--**---****---*********---********-----******----' => '9',

		);
		$get_captcha='';
		for($i = 1; $i <= count($word)-1; $i++){
			$percent = 0;
			$num = 0;
		    foreach($key as $tmp_key1 => $tmp_val1){
		    	similar_text($word[$i]['key'], $tmp_key1, $tmp_percent);
		    	if($percent < $tmp_percent){
		    		$percent = $tmp_percent;
		    		$num = $tmp_val1;
		    	}
		        
		    }
		    $get_captcha = $get_captcha.$num;
		}

		//echo '驗證碼為: '.$get_captcha.PHP_EOL;

		//登入
		$loginurl = 'http://ag.boy999.com/DataPage/LoginHandler.ashx';
		$post = array(

			'type'=>'lo',
			'name'=>'boscs@j80000',
			'password'=>'1aRtoWpbv9',
			'vcode'=>$get_captcha,

		);

		$login = $this->bodytool($loginurl, $header, 'POST', $this->jar, $post);


		//取得平台id
		$accid_url = 'http://ag.boy999.com/DataPage/ReportHandler.ashx';
		$post = array(

			'type'=>'winlosenew',
        	'accid'=>'134540',
        	'acctype'=>'0',
        	'from'=>$s_date,
        	'to'=>$e_date,
        	'ptid'=>'0',
        	'gametype'=>'1,2,3,4,5,6,7,8',
        	'account'=>'',
        	'test'=>'1',
        	'simple'=>'0',
        	'co'=>'null',
        	'subsite'=>'0',

		);

		$accid = $this->bodytool($accid_url, $header, 'POST', $this->jar, $post);
		$accid = json_decode($accid,true);

		$data_url = 'http://ag.boy999.com/DataPage/ReportHandler.ashx';
		foreach ($accid as $key => $value) {
			$post[$key] = array(
				'type'=>'winlosenew',
	        	'accid'=>$value[0][1],
	        	'acctype'=>'0',
	        	'from'=>$s_date,
	        	'to'=>$e_date,
	        	'ptid'=>'0',
	        	'gametype'=>'1,2,3,4,5,6,7,8',
	        	'account'=>'',
	        	'test'=>'1',
	        	'simple'=>'0',
	        	'co'=>'null',
	        	'subsite'=>'0',
			);
		}

		$data = $this->do_pool($data_url, $header, $this->jar, $post);
		$regex = "#^(companyprefix|^".implode("|^",$prefix).")#";
		foreach ($data as $key => $value) {
			$pre_acc = json_decode($value,TRUE);
			foreach ($pre_acc as $pre_key => $pre_value) {
				$account = substr($pre_value[0][3],6);
				if(preg_match($regex,$account,$match)){
					if(isset($total[$match[1]])){
						$total[$match[1]]['payout'] += round($pre_value[3],2);
					}else{
						$total[$match[1]]['prefix'] = $match[1];
						$total[$match[1]]['payout'] += round($pre_value[3],2);
					}
				}
			}
			
		}
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		// print_r($total);
		return $total;

	}

}

// $x=new crown();
// $x->index('2019-07-01','2019-07-02');