
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

class sbo extends tools{

	public function index($s_date,$e_date,$game=null){

		//login page
		$jar=new CookieJar;
		$loginurl='https://admin.sswwkk.com/login';
		$header=array(

			'accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
			// 'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36',

		);
		$logbody=$this->bodytool($loginurl,$header,'GET',$jar,null);

		$dom=new DOMDocument();
        @$dom->loadHTML($logbody);
        $token=$dom->getElementById("loginForm")->getElementsByTagName("input")->item(0)->attributes->item(2)->nodeValue;
 

        //login url
       

		$post=array(
			'_token'=>$token,
		    'Username'=>'boscs',
		    'Password'=>'568winwin',
		    'btnSubmit'=>'Login',

		);
		$logbody=$this->bodytool($loginurl,$header,'POST',$jar,$post);
		$dom=new DOMDocument();
		@$dom->loadHTML($logbody);

		// $code = $dom->getElementsByTagName("input")->item(0)->attributes->item(2)->nodeValue;
		// $id_token = $dom->getElementsByTagName("input")->item(1)->attributes->item(2)->nodeValue;
		// $scope = $dom->getElementsByTagName("input")->item(2)->attributes->item(2)->nodeValue;
		// $state = $dom->getElementsByTagName("input")->item(3)->attributes->item(2)->nodeValue;
		// $session_state = $dom->getElementsByTagName("input")->item(4)->attributes->item(2)->nodeValue;

		$reloginurl1 = "https://admin.sswwkk.com/apicustomer/winlose";

		//echo $s_date;

		$post1 = array(
			'_token'=> $token,
			'fromDate'=> $s_date,
			'toDate'=> $e_date,
			'gametype'=>'all',
			'decimal-point'=> 'on'
		);
		$rebody=$this->bodytool($reloginurl1,$header,'POST',$jar,$post1);
		$rebody=json_decode($rebody,1);
		$dom=new DOMDocument();
		@$dom->loadHTML($rebody['content']);

		$tar = $dom->getElementsByTagName("tbody");
		$rows = $tar->item(0)->getElementsByTagName("tr");
		
		
		$total=[];
		if($game=='1'){
			$dont_array = ['VirtualSports','(SBO) Slots','All Products'];
			$type_flag = false;
			foreach ($rows as $k => $row)
			{
				if(trim($row->nodeValue)=='Sports'){
					$type_flag = true;
					continue;
				}
				
				if($type_flag)
				{
					if(in_array(trim($row->nodeValue), $dont_array))
					{
						break;
					}else{
						$prefix = strtolower(trim($row->getElementsByTagName("td")->item(0)->nodeValue));

						$payout = trim($row->getElementsByTagName("td")->item(5)->nodeValue);
						$payout = (float)str_replace(',', '', $payout);

						preg_match('/bos(.+)/i',$prefix,$prefix);
						if($payout!=0 && $prefix!=null){
							if(isset($total[$prefix[1]])){
								$total[$prefix[1]]['payout'] 	=	$total[$prefix[1]]['payout']+$payout;
							}else{
								$total[$prefix[1]]=[
									'prefix'=>$prefix[1],
									'payout'=>$payout,
								];	
							}
						}
					}
				}else{
					continue;
				}
				
			}
		}
		
		if($game=='2'){
			$dont_array = ['VirtualSports','Sports','All Products'];
			$type_flag = false;
			foreach ($rows as $k => $row)
			{
				if(trim($row->nodeValue)=='(SBO) Slots'){
					$type_flag = true;
					continue;
				}
				
				if($type_flag)
				{
					if(in_array(trim($row->nodeValue), $dont_array))
					{
						break;
					}else{
						$prefix = strtolower(trim($row->getElementsByTagName("td")->item(0)->nodeValue));

						$payout = trim($row->getElementsByTagName("td")->item(5)->nodeValue);
						$payout = (float)str_replace(',', '', $payout);

						preg_match('/bos(.+)/i',$prefix,$prefix);
						if($payout!=0 && $prefix!=null){
							if(isset($total[$prefix[1]])){
								$total[$prefix[1]]['payout'] 	=	$total[$prefix[1]]['payout']+$payout;
							}else{
								$total[$prefix[1]]=[
									'prefix'=>$prefix[1],
									'payout'=>$payout,
								];	
							}
						}
					}
				}else{
					continue;
				}
				
			}
			// $standard=10000;
			// foreach ($rows as $k => $row)
			// {

				
			// 	if(trim($row->nodeValue)=='(SBO) Slots'){
			// 		$standard=$k;
			// 		continue;
			// 	}
			// 	if($row->nodeValue=='Sports'){
			// 		continue;
			// 	}
			// 	if(trim($row->nodeValue)=='VirtualSports'){
			// 		break;
			// 	}
			// 	if(trim($row->nodeValue)=='All Products'){
			// 		break;
			// 	}
			// 	if($k>$standard){
			// 		$prefix = strtolower(trim($row->getElementsByTagName("td")->item(0)->nodeValue));
			// 		$payout = trim($row->getElementsByTagName("td")->item(5)->nodeValue);
			// 		$payout = (float)str_replace(',', '', $payout);

			// 		preg_match('/bos(.+)/i',$prefix,$prefix);
			//       	if($payout!=0 && $prefix!=null){
			// 		    if(isset($total[$prefix[1]])){
			//       			$total[$prefix[1]]['payout'] 	=	$total[$prefix[1]]['payout']+$payout;
			//       		}else{
			//       			$total[$prefix[1]]=[
			// 			    	'prefix'=>$prefix[1],
			// 			    	'payout'=>$payout,
			// 			    ];	
			//       		}
			// 		}
			// 	}
				
			// }
		}
		if($game=='3'){
			$dont_array = ['(SBO) Slots','Sports','All Products'];
			$type_flag = false;
			foreach ($rows as $k => $row)
			{
				if(trim($row->nodeValue)=='VirtualSports'){
					$type_flag = true;
					continue;
				}
				
				if($type_flag)
				{
					if(in_array(trim($row->nodeValue), $dont_array))
					{
						break;
					}else{
						$prefix = strtolower(trim($row->getElementsByTagName("td")->item(0)->nodeValue));

						$payout = trim($row->getElementsByTagName("td")->item(5)->nodeValue);
						$payout = (float)str_replace(',', '', $payout);

						preg_match('/bos(.+)/i',$prefix,$prefix);
						if($payout!=0 && $prefix!=null){
							if(isset($total[$prefix[1]])){
								$total[$prefix[1]]['payout'] 	=	$total[$prefix[1]]['payout']+$payout;
							}else{
								$total[$prefix[1]]=[
									'prefix'=>$prefix[1],
									'payout'=>$payout,
								];	
							}
						}
					}
				}else{
					continue;
				}
				
			}
			// $standard=10000;
			// foreach ($rows as $k => $row)
			// {

				
			// 	if(trim($row->nodeValue)=='(SBO) Slots'){
			// 		continue;
			// 	}
			// 	if($row->nodeValue=='Sports'){
			// 		continue;
			// 	}
			// 	if(trim($row->nodeValue)=='VirtualSports'){
			// 		$standard=$k;
			// 	}
			// 	if(trim($row->nodeValue)=='All Products'){
			// 		break;
			// 	}
			// 	if($k>$standard){
			// 		$prefix = strtolower(trim($row->getElementsByTagName("td")->item(0)->nodeValue));
			// 		@$payout = trim($row->getElementsByTagName("td")->item(5)->nodeValue);
			// 		$payout = (float)str_replace(',', '', $payout);

			// 		preg_match('/bos(.+)/i',$prefix,$prefix);
			//       	if($payout!=0 && $prefix!=null){
			// 		    if(isset($total[$prefix[1]])){
    		//       			$total[$prefix[1]]['payout'] 	=	$total[$prefix[1]]['payout']+$payout;
    		//       		}else{
    		//       			$total[$prefix[1]]=[
    		// 			    	'prefix'=>$prefix[1],
    		// 			    	'payout'=>$payout,
    		// 			    ];	
    		//       		}
			// 		}
			// 	}
				
			// }
		}
		

	
		// print_r($tmp_data);
		return $total;
	}
}

// $x=new bti;
// $x->index('2019-11-01','2019-11-17');

