<?php
//require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class imone extends tools{

	public function index($s_date,$e_date,$game=null){


		$jar=new CookieJar;
		

		$loginurl='http://rbos.aegis88.com/Auth/Login';
		$header=array(
			'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
		);
		
		$post=array(
			'Language'=>'ZH-CN',
			'Username'=>'boscs',
			'Password'=>'aabb0987',
			'Captcha'=>$vcode,
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);

		$query;
		do{
			$vurl='http://rbos.aegis88.com/Auth/GenerateCaptcha';
			$pic=$this->bodytool($vurl,null,'GET',$jar,null);
			
			$vcode = $this->getcaptcha($pic,150);

			

			$loginurl='http://rbos.aegis88.com/Auth/Login';
			$header=array(
				'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
			);
			
			$post=array(
				'Language'=>'ZH-CN',
				'Username'=>'boscs',
				'Password'=>'aabb0987',
				'Captcha'=>$vcode,
			);
			$login = $this->bodytool($loginurl,$header,'POST',$jar,$post);
			$dom = new DOMDocument();
			@$dom->loadHTML($login);
			$xpath = new DOMXpath($dom);
			$query = $xpath->query("//div[@class='error']/span")->item(0)->nodeValue;
			
		}while($query=='无效验证码');

		$reurl='http://rbos.aegis88.com/Report/OverallPerformanceReport/SearchOverallPerformanceReport';
		$header3=array(
			'Accept'=>'application/json, text/javascript, */*; q=0.01',
			//'Cookie'=>$cookie,
			'X-Requested-With'=>'XMLHttpRequest',
		);
		$post2=array(

			'sEcho'=>'1',
			'iColumns'=>'18',
			'sColumns'=>',,,,,,,,,,,,,,,,,',
			'iDisplayStart'=>'0',
			'iDisplayLength'=>'-1',
			'mDataProp_0'=>'',
			'sSearch_0'=>'',
			'bRegex_0'=>'false',
			'bSearchable_0'=>'true',
			'bSortable_0'=>'false',
			'mDataProp_1'=>'Operator',
			'sSearch_1'=>'',
			'bRegex_1'=>'false',
			'bSearchable_1'=>'true',
			'bSortable_1'=>'true',
			'mDataProp_2'=>'Product',
			'sSearch_2'=>'',
			'bRegex_2'=>'false',
			'bSearchable_2'=>'true',
			'bSortable_2'=>'false',
			'mDataProp_3'=>'ActivePlayer',
			'sSearch_3'=>'',
			'bRegex_3'=>'false',
			'bSearchable_3'=>'true',
			'bSortable_3'=>'true',
			'mDataProp_4'=>'NoOfBets',
			'sSearch_4'=>'',
			'bRegex_4'=>'false',
			'bSearchable_4'=>'true',
			'bSortable_4'=>'true',
			'mDataProp_5'=>'CurrencyCode',
			'sSearch_5'=>'',
			'bRegex_5'=>'false',
			'bSearchable_5'=>'true',
			'bSortable_5'=>'true',
			'mDataProp_6'=>'Turnover',
			'sSearch_6'=>'',
			'bRegex_6'=>'false',
			'bSearchable_6'=>'true',
			'bSortable_6'=>'true',
			'mDataProp_7'=>'ActualTurnover',
			'sSearch_7'=>'',
			'bRegex_7'=>'false',
			'bSearchable_7'=>'true',
			'bSortable_7'=>'true',
			'mDataProp_8'=>'Tips',
			'sSearch_8'=>'',
			'bRegex_8'=>'false',
			'bSearchable_8'=>'true',
			'bSortable_8'=>'true',
			'mDataProp_9'=>'Win',
			'sSearch_9'=>'',
			'bRegex_9'=>'false',
			'bSearchable_9'=>'true',
			'bSortable_9'=>'true',
			'mDataProp_10'=>'Adjustment',
			'sSearch_10'=>'',
			'bRegex_10'=>'false',
			'bSearchable_10'=>'true',
			'bSortable_10'=>'true',
			'mDataProp_11'=>'WinLoss',
			'sSearch_11'=>'',
			'bRegex_11'=>'false',
			'bSearchable_11'=>'true',
			'bSortable_11'=>'true',
			'mDataProp_12'=>'ProviderBonus',
			'sSearch_12'=>'',
			'bRegex_12'=>'false',
			'bSearchable_12'=>'true',
			'bSortable_12'=>'true',
			'mDataProp_13'=>'Percentages',
			'sSearch_13'=>'',
			'bRegex_13'=>'false',
			'bSearchable_13'=>'true',
			'bSortable_13'=>'true',
			'mDataProp_14'=>'ProgressiveBet',
			'sSearch_14'=>'',
			'bRegex_14'=>'false',
			'bSearchable_14'=>'true',
			'bSortable_14'=>'true',
			'mDataProp_15'=>'ProgressiveWin',
			'sSearch_15'=>'',
			'bRegex_15'=>'false',
			'bSearchable_15'=>'true',
			'bSortable_15'=>'true',
			'mDataProp_16'=>'Bonus',
			'sSearch_16'=>'',
			'bRegex_16'=>'false',
			'bSearchable_16'=>'true',
			'bSortable_16'=>'true',
			'mDataProp_17'=>'Commission',
			'sSearch_17'=>'',
			'bRegex_17'=>'false',
			'bSearchable_17'=>'true',
			'bSortable_17'=>'true',
			'sSearch'=>'',
			'bRegex'=>'false',
			'iSortCol_0'=>'5',
			'sSortDir_0'=>'asc',
			'iSortingCols'=>'1',
			'DateFrom'=>$s_date,
			'DateTo'=>$e_date,
			'SelectedOperator'=>'',
			'SelectedReseller'=>'-1',
			'SelectedCurrency'=>'',
			'SelectedDisplayCurrency'=>'',
			'IsSearch'=>'true',
			'SelectedProductType[]'=>'6',
			'SelectedReport'=>'1',

		);
		$re=$this->bodytool($reurl,$header3,'POST',$jar,$post2);
		$report=json_decode($re);
		//print_r($report);exit;
		foreach ($report->aaData as $k => $v) {
			$payout=$v->SubPerformanceReportList[0]->TotalWinLoss;
			preg_match('/bos(.+)prod/',$v->Operator,$prefix);
			
				$total[]=[
					'prefix'=>$prefix[1],
					'payout'=>$v->SubPerformanceReportList[0]->TotalWinLoss,
				];
			
		}
		//print_r($total);exit;
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);      
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
		        	$im_data[$x][$y] = '*';          
	                //echo'*';

	            }
	            else{
	            	$im_data[$x][$y] = '-';
	                //echo' ';
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
					
					$im_data[$x-1][$y-1] == '-' ? $noice++ : null;
					$im_data[$x][$y-1] == '-' ? $noice++ : null;
					$im_data[$x+1][$y-1] == '-' ? $noice++ : null;
					$im_data[$x-1][$y] == '-' ? $noice++ : null;
					$im_data[$x+1][$y] == '-' ? $noice++ : null;
					$im_data[$x-1][$y+1] == '-' ? $noice++ : null;
					$im_data[$x][$y+1] == '-' ? $noice++ : null;
					$im_data[$x+1][$y+1] == '-' ? $noice++ : null;
					if($noice >= 6){
						$im_data[$x][$y] = '-';
					}
				}
				
			}
		}

		//顯示躁點後的圖片
		// for($y = 0; $y < imagesy($img); $y++){
		// 	for($x = 0; $x < imagesx($img); $x++){
			
		// 		echo $im_data[$x][$y];
		// 	}
		// 	echo "\n";
	
		// }
	    

	    //判斷數字起始點
	    $key_width=15; //數字寬
	    $key_heigh=20; //數字長
	    $key_n=300; //一個數字的總數

	    $w = 0;
	    $w_t = 0;
	    $k = 1;
	    $h_h = 0;

	    //判斷數字起始和結束
	    for($i=16; $i<imagesx($img)-20; $i++){ //width
	        for($j=6; $j<imagesy($img); $j++){ //height
	                if($w_t==0){
	                    $word[$k]['width_s'] = $i; //數字開頭位於圖片的寬
	                    $word[$k]['width_e'] = $i + $key_width - 1;
	                    $w_t = 1;
	                    $w = $i;
	                }
	                if($h_h == 0){
	                    $h_h = $j;
	                }
	                if($h_h >= $j){
	                    $h_h = $j;
	                    $word[$k]['heigh_h'] = $h_h;
	                    $word[$k]['heigh_l'] = $h_h + ($key_heigh - 1);
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
	    	'------*****--------********------**********-----****---***----****----**-----****----****--****-----****--****-----****--*-**-----***--****------***--****-----****--****-----****--****-----****--****-----**----****----****---****----***----****---****-----*********-------*******---------*****-------' => '0',
	    	'---------**------------***-----------****---------******-------********------*********------****-***------------***-----------****-----------****-----------****-----------***------------***-----------*****----*-----****-----*-----****-----*-----***------------***------------***------------***-------' => '1',
	    	'---------**------------***-----------*****--------******------*********------*********------****-***------------***-----------****-----------****-----------**-*-----------***------------***-----------****-----------****-----------****-----------***------------***-----------****-----------****-------' => '1',
	    	'-----*****--------*********------**********----****---****----****----***----***-----***-----------****-----------****----------****----------*****---------*****---------*****---------*****---------*****---------*****----------****----------***********---************---***********----***********----' => '2',
	    	'-----******--------********------**********----****---****----***-----***-----**-----***-----------****----------*****----------****---------***------------***-*------------****-----------****-----------****---***-----****---****----****---****-*-****----***********-----*********--------*****-------' => '3',
	    	'---------****----------*****---------*****----------*****---------******--------*******-------********-------***-***-------***--***------****--***-----****--****-----***---****----****---***-----*************--*************--************---------****----------****------------***------------***------' => '4',
	    	'----**-******------*********-----*********------*********------***-----------****-----------****-----------********-------***-******----***********----****----****-----------****------------****----------****--****-----****--****-----***----***---*****----**********------********--------**-***------' => '5',
	    	'----*********-----**********-----**********-----**********-----***-----------****----------*****----------*********-------**********----***********----****-*--****-----------****-----------***------------****--****-----****--****-----***---*****--*****----**********------********--------*****-------' => '5',
	    	'------*****---------*******-------*********-----****--****-----***----***----****-----------****----------*********------**********-----***********----*****--****----****----***----****----****---***-----***----***-----***----***----****----****---****-----*********------********---------*****------' => '6',
	    	'--************---************--*************--************-----------***-----------****-----------****----------***-----------****-----------***-----------****-----------***-----------****-----------***-----------****-----------****-----------***------------***-----------****-----------***----------' => '7',
	    	'-----*****---------********------*********-----****---****----***-----***----***-----***----***-----***----****---***------*********------*******------**********-----****---****---***-----****---****-----***---****-----***---****----****---****---****-----**********-----*********--------*****-------' => '8',
	    	'-----*****---------*******-------*********-----****---****----***----****---****-----***---****-----***---****-----***---****----****---****----****---****-*-*****----***********-----**********------********-----------****----***---****----****---****-----*********------********---------****--------' => '9',

	    );

	    //比對文字
	    $get_captcha='';
	    for($i = 1; $i <= count($word); $i++){
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
	    //echo $get_captcha.PHP_EOL;
	    return $get_captcha;
	       
	}
}
// $x=new imone;
// $x->index('2019-06-01','2019-06-02');