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

class bos extends tools{

	public function index($s_date,$e_date,$game=null){

		$s_date = date('Y-m-d', strtotime($s_date));
        $e_date = date('Y-m-d', strtotime($e_date));


		$jar=new CookieJar;
		do{
			//取得驗證圖
			$pic_url = 'https://bo.boscp8.com/api/checkcode.php';
			$pic = $this->bodytool($pic_url, null, 'GET', $jar, null);
			
			//登入
			$login_url = 'https://bo.boscp8.com/index.php';
			$header=array(	
				'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			);
			$post = array(
				'username'=>'boscs',
				'password'=>'ga9Er7Wa',
				'checkcode'=>$this->getcaptcha($pic,1),
				'action'=>'login',
				'PutBut'=>'登　陆',
			);
			$login = $this->bodytool($login_url, $header, 'POST', $jar, $post);
			$dom = new DOMDocument();
			@$dom->loadHTML($login);
			$xpath = new DOMXpath($dom);
			$query = $xpath->query("//div[@class='msg']")->item(0)->nodeValue;
		}while($query == '验证码错误！');
		
		
		//取得輸贏
		$data_url = 'https://bo.boscp8.com/user.php?mod=winLoseTable';
		$post = array(
			'sub'=>'1',
			'starttime'=>$s_date,
			'endtime'=>$e_date,
			'data[agent]'=>'000',
			'ctl00$ctl00$Base$ContentPlaceHolder1$IButSearch.x'=>'45',
			'ctl00$ctl00$Base$ContentPlaceHolder1$IButSearch.y'=>'9',
		);
		$data = $this->bodytool($data_url, $header, 'POST', $jar, $post);
		$dom = new DOMDocument();
		@$dom->loadHTML($data);
		$xpath = new DOMXpath($dom);

		$query = $xpath->query("//table[@class='tablesorter']//tbody//tr");
		$n = 0;
		foreach ($query as $tmp_key1) {
		    $td = $xpath->query('td', $tmp_key1);
		    foreach ($td as $td_key) {
		    	$total[$n]['prefix'] = substr($td->item(0)->nodeValue, 3);
		        $total[$n]['payout'] =str_replace(',', '', $td->item(6)->nodeValue);   
		    }   
		    $n++;
		}

		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);
		//print_r($total);
		return $total;

	}

	public function getcaptcha($img, $value){

		//判斷圖片
	   	$img=imagecreatefromstring($img);

		for ($y=0;$y<imagesy($img);$y++) {          

		    for ($x=0;$x<imagesx($img);$x++) {

		        $rgb = imagecolorat($img,$x,$y);
		        $r = ($rgb>>16) & 0xFF;
		        $g = ($rgb>>8) & 0xFF;
		        $b = $rgb & 0xFF;
		        if($b < $value ){
		        	$im_data[$x][$y] = '-';          
	                //echo'*';

	            }
	            else{
	            	$im_data[$x][$y] = '*';
	                //echo'-';
	            }
		    }
		   	//echo "\n";            
		}
		
		//消除躁點
		$noice = 0; 
		for($x = 0; $x < imagesx($img); $x++){
			for($y = 0; $y < imagesy($img); $y++){
				$noice = 0;
				if($im_data[$x][$y] == '*'){
					if(($x-1) >= 0 && ($y-1) >=0){
						$im_data[$x-1][$y-1] == '-' ? $noice++ : null;
					}
					if(($y-1) >= 0){
						$im_data[$x][$y-1] == '-' ? $noice++ : null;
					}
					if(($x+1) < imagesx($img) && ($y-1) >=0){
						$im_data[$x+1][$y-1] == '-' ? $noice++ : null;
					}
					if(($x-1) >= 0){
						$im_data[$x-1][$y] == '-' ? $noice++ : null;
					}
					if(($x+1) < imagesx($img)){
						$im_data[$x+1][$y] == '-' ? $noice++ : null;
					}
					if(($x-1) >= 0 && ($y+1) < imagesy($img)){
						$im_data[$x-1][$y+1] == '-' ? $noice++ : null;
					}
					if(($y+1) < imagesy($img)){
						$im_data[$x][$y+1] == '-' ? $noice++ : null;
					}
					if(($x+1) < imagesx($img) && ($y+1) < imagesy($img)){
						$im_data[$x+1][$y+1] == '-' ? $noice++ : null;
					}
					if($noice >= 6){
						$im_data[$x][$y] = '-';
					}
				}
				
			}
		}

		#印出消除躁點後的圖片
		// for($y = 0; $y < imagesy($img); $y++){
		// 	for($x = 0; $x < imagesx($img); $x++){
			
		// 		echo $im_data[$x][$y];
		// 	}
		// 	echo "\n";
		// }

		$word = array(
			
			'---*******-----****-****---*****-*****-*****---***********--**********---**********---***********--**********---**********---**********---*****-*****-*****---****-****-----*******---' => array(
				'width' => '13',
				'num' => '0',
			),
			'---*******-----****-****---*****-*****-*****---**********---***********--**********---**********---**********---**********---**********---*****************---****-****-----*******---' => array(
				'width' => '13',
				'num' => '0',
			),
			'---*******-----****-****---*****-*****-******--**********---**********---**********--***********---**********---***********--**********--******-*****-*****---****-****-----*******---' => array(
				'width' => '13',
				'num' => '0',
			),
			'---*******----*****-****---*****-*****-*****---**********---**********---**********---**********---**********---**********--***********---*****-*****-*****--*****-****-----*******---' => array(
				'width' => '13',
				'num' => '0',
			),
			'*****-*****-*****-*****-*****-*****-*****-***********-*****-*****-*****-*****-******' => array(
				'width' => '6',
				'num' => '1',
			),
			'*****-*****-*****-*****-*****-*****-*****-*****-*****-*****-*****-*****-*****-******' => array(
				'width' => '6',
				'num' => '1',
			),
			'*****-*****-*****-*****-*****-*****-*****-*****-*****-*****-***********-*****-******' => array(
				'width' => '6',
				'num' => '1',
			),
			'--********--****--********----**********---*********--**********-*******-**---*****------*****------****--------**----**--**********-***********************************' => array(
				'width' => '12',
				'num' => '2',
			),
			'--**--****---**---********----**********--**********---*********--******-**---*****-----******------****-*-----***----**--**********-***********************************' => array(
				'width' => '12',
				'num' => '2',
			),
			'---**--****---***---*****--****---****--****---****--****---****---**---*****----**-*****---------******-***---***********---**********---**********--**********---*****--****--***---' => array(
				'width' => '13',
				'num' => '3',
			),
			'---********----**---*****--****--*****--****---****--****--*****---**---*****----**-*****---------**********---***********---**********---**********---*********--******--*********---' => array(
				'width' => '13',
				'num' => '3',
			),
			'---********----**--******--*****-*****--*****--****--*****--****---**---*****---*********-----*-*-******-***---***********---**********---**********---*********-*-*****--*********---' => array(
				'width' => '13',
				'num' => '3',
			),
			'---------**----------***---------****--------*****-------******-------******------**-****-----**--****----**---****---**----****--*************-------****---------****---------*****-' => array(
				'width' => '13',
				'num' => '4',
			),
			'---------**---------****---------****--------*****------*******------********-----**-****-----**-*****----**---*****--**-*--****--************--------****---------******-------******' => array(
				'width' => '13',
				'num' => '4',
			),
			'--*********---*********---********----*******----**----------***--****---**---*****--*----******------***********--**********--**********-***********-*****--********---' => array(
				'width' => '12',
				'num' => '5',
			),
			'---*******----****-****--*****-****-******-****-*****---**--***********-*****************--**********--**********-***********--*****-*****-*****--*********----*******--' => array(
				'width' => '12',
				'num' => '6',
			),
			'-***********-***********-**********-**********--**------**---------**--------***--------****-------****-------*****-------*****------*****-------*****-------*****------' => array(
				'width' => '12',
				'num' => '7',
			),
			'---***--------***----**--****----**--****-----*--******--**--*********---**********---**********-**************---********-----*****-**-----****-***----***----**---**--' => array(
				'width' => '12',
				'num' => '8',
			),
			'--*******----***--****--****--**********---*********---*********---*********---**********-******--**************--**********--**********--*****-****--****---********---' => array(
				'width' => '12',
				'num' => '9',
			),
		);

		//找出相似數值位於圖片的位置等
		$tmp_x;
		$tmp_key = '';
		$percent = 0;
		$n = 0;
		foreach ($word as $word_key => $word_value) {
			
			for($x = 6; $x < imagesx($img)-7; $x++){
				
				for($y = 8; $y < imagesy($img)-6; $y++){
					
					if(($x + $word_value['width']) <= imagesx($img)){
						for($i = 0; $i < $word_value['width']; $i++){
							$tmp_key = $tmp_key.$im_data[$x+$i][$y];
						}
					}
				}
						
				similar_text($word_key, $tmp_key, $tmp_percent);
				if($percent < $tmp_percent){
					$percent = $tmp_percent;
					if($percent > 90){
						$tmp_x = $x;
						if(isset($end)){
							$end = $this->rep($end, $x, $percent, $word_value);
						}
						else{
							$n++;
							$end[$n]['start'] = $x;
							$end[$n]['percent'] = $percent;
							$end[$n]['word'] = $word_value['num'];
							$end[$n]['width'] = $word_value['width'];
						}
					}
					
				}
				$tmp_key = '';
				if(isset($tmp_x)){
					if(($tmp_x + $word_value['width']) < $x){
						$tmp_x = '';
						$percent = '';
					}
				}
				
			}	
			$percent = '';
			
		}

		//依照開始位置排序
		$en_start = array();
		foreach ($end as $value) {
			$en_start[] = $value['start'];
		}
		array_multisort($en_start, SORT_ASC, $end);

		//整理
		$nn = 0;
		foreach ($end as $value) {
			if(!isset($num)){
				$nn++;
				$num[$nn]['start'] = $value['start'];
				$num[$nn]['percent'] = $value['percent'];
				$num[$nn]['word'] = $value['word'];
			}
			else{
				if(($num[$nn]['start']+$value['width']-1) > $value['start'] && $value['word'] == $num[$nn]['word']){	
					if($value['percent'] > $num[$nn]['percent']){
						$num[$nn]['start'] = $value['start'];
						$num[$nn]['percent'] = $value['percent'];
						$num[$nn]['word'] = $value['word'];
					}
				}
				else{
					$nn++;
					$num[$nn]['start'] = $value['start'];
					$num[$nn]['percent'] = $value['percent'];
					$num[$nn]['word'] = $value['word'];
				}
			}
		}

		//排序
		$start = array();
		foreach ($num as $value) {
			$start[] = $value['start'];
		}
		array_multisort($start, SORT_ASC, $num);
		$get_captcha = '';
		foreach ($num as $value) {
			$get_captcha = $get_captcha.$value['word'];
		}
		//echo $get_captcha;
		return $get_captcha;
	       
	}


	function rep($end, $x, $percent, $word_value){
		
		$n = count($end);
		$hav = 0;
		foreach ($end as $key=>$value) {
			if($value['start'] == $x){
				if($value['percent'] < $percent){
					$end[$key]['start'] = $x;
					$end[$key]['percent'] = $percent;
					$end[$key]['word'] = $word_value['num'];
					$end[$key]['width'] = $word_value['width'];
					$hav++;	
				}
				else{
					$hav++;
				}

			}
		}
		if($hav == 0){
			$n++;
			$end[$n]['start'] = $x;
			$end[$n]['percent'] = $percent;
			$end[$n]['word'] = $word_value['num'];
			$end[$n]['width'] = $word_value['width'];
		}
		return $end;

	}
   
}

// $x=new bos;
// $x->index('2019-07-01','2019-07-02');