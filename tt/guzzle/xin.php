<?php 
//require_once __DIR__ . '/../../../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SetCookie;
require_once 'class.tools.php';

class xin extends tools{

	public function index($s_date,$e_date,$game=null){

		$sd = date('Y-m-d', strtotime($s_date));
        $ed = date('Y-m-d', strtotime($e_date));

       	$inter = array(
            'DA6' => array(
                'username' => 'dbaet6',
                'password' => 'hcTY9rby',
            ),
            'DA7' => array(
                'username' => 'dyaun7',
                'password' => 'cvNzvfym',
            ),
            'DA8' => array(
                'username' => 'djain8',
                'password' => 'VwEEKhbB',
            ),
            'DA9' => array(
                'username' => 'daawi9',
                'password' => 'uRW8R8MD',
            ),
            'DB0' => array(
                'username' => 'dybin0',
                'password' => '53UTB2fe',
            ),
            'DB1' => array(
                'username' => 'dxbpj1',
                'password' => '9XwdZYSb',
            ),
            'DB2' => array(
                'username' => 'dabdf2',
                'password' => 'GYYHcx7g',
            ),
            'DB3' => array(
                'username' => 'dabty3',
                'password' => '6P7TdEFG',
            ),
            'DB4' => array(
                'username' => 'dbbbs4',
                'password' => 'c58pqUbR',
            ),
            'DB5' => array(
                'username' => 'dhbhg5',
                'password' => '9tACA3JK',
            ),
            'DB6' => array(
                'username' => 'dxbxj6',
                'password' => 'uf3gMJsY',
            ),
            'DB7' => array(
                'username' => 'dbbbl7',
                'password' => 'KXaj3rXT',
            ),
            'DK6' => array(
                'username' => 'dbkbt6',
                'password' => 'gD6e5P4Z',
            ),
            'EX3' => array(
                'username' => 'ejxjs3',
                'password' => 'hne2rZpL',
            ),
        );
       	$num = 0;
        foreach ($inter as $in_key => $in_value) {
        	echo "現在是:  $in_key".PHP_EOL;
        	$jar = new CookieJar;


			//取得登入頁面
			$login_page_url = 'https://data.xingaming.net/jsp/loginPage.htm?err=';
			$header = [
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
			];
			$login_page = $this->bodytool($login_page_url,$header,'GET',$jar,null);
			preg_match_all('/(?<=crypt.setp\x28\x22)(.*)(?=\x22)/', $login_page, $pu_k);

			//取得驗證碼
			$captcha_url = 'https://data.xingaming.net/jsp/captcha.htm';
	        $captcha_pic = $this->bodytool($captcha_url,$header,'GET',$jar,null);

	        //登入
	        $public_key = '-----BEGIN PUBLIC KEY-----'.PHP_EOL.wordwrap($pu_k[0][0], 64, "\n", true).PHP_EOL.
	                    
'-----END PUBLIC KEY-----';
			openssl_public_encrypt($in_value['username'], $usr_encrypted, $public_key);
			$usr_encrypted = base64_encode($usr_encrypted);
			openssl_public_encrypt($in_value['password'], $pas_encrypted, $public_key);
			$pas_encrypted = base64_encode($pas_encrypted);

			$login_url = 'https://data.xingaming.net/j_spring_security_check';
			$post = array(
				'captcha'=>$this->captcha($captcha_pic,160),
				'j_username'=>$usr_encrypted,
				'j_password'=>$pas_encrypted,
			);
			$login = $this->bodytool($login_url,$header,'POST',$jar,$post);
			
			
			
			if($game == 1){
				//取得免費旋轉筆數
				$num_url = 'https://data.xingaming.net/jsp/secured/promotionAction_queryPromotionMember.htm?_dc='.time();
				$num_post = array(
					'fromTime'=>"$s_date 00:00:00",
					'toTime'=>"$e_date 23:59:59",
					'promotionType'=>'0',
					'promotionCode'=>'',
					'userName'=>'',
					'likeQueryLoginName'=>'false',
					'sortOrder'=>'beginTime',
					'sortBy'=>'DESC',
					'pageNum'=>'25',
					'currency'=>'',
					'status'=>'4',
					'productId'=>$in_key,
					'page'=>'1',
					'start'=>'0',
					'limit'=>'25',
				);
				$num_data = $this->bodytool($num_url,$header,'POST',$jar,$num_post);
				$dom = new DOMDocument();
				@$dom->loadXML($num_data);
				$num_query = @$dom->getElementsByTagName('totalCount') ;
				foreach ($num_query as $key => $value) {
				   $num = $value->nodeValue;
				}

				//取得免費旋轉已兌換資料
				$data_url = 'https://data.xingaming.net/jsp/secured/promotionAction_queryPromotionMember.htm?_dc='.time();
				$data_post = array(
					'fromTime'=>"$s_date 00:00:00",
					'toTime'=>"$e_date 23:59:59",
					'promotionType'=>'0',
					'promotionCode'=>'',
					'userName'=>'',
					'likeQueryLoginName'=>'false',
					'sortOrder'=>'beginTime',
					'sortBy'=>'DESC',
					'pageNum'=>$num,
					'currency'=>'',
					'status'=>'4',
					'productId'=>$in_key,
					'page'=>'1',
					'start'=>'0',
					'limit'=>$num,
				);
				$data = $this->bodytool($data_url,$header,'POST',$jar,$data_post);

				//取得站台
				$plat = $this->prefixlist();
				$xin_data;
				$dom = new DOMDocument();
                @$dom->loadHTML($data);
                $xpath = new DOMXpath($dom);
                //金額
                $amount_query = $xpath->query("//exchangeamount");
                foreach ($amount_query as $key => $value) {
                   $amount[$key] = $value->nodeValue;
                }
                $username_query = $xpath->query("//username");
                foreach ($username_query as $key => $value) {
                    foreach ($plat as $p_key => $p_value) {
                        if(substr($value->nodeValue, 0, strlen($p_value)) == $p_value){
                            $xin_data[$p_value]['payout'] += $amount[$key];
                        }
                    }
                   
                }
                foreach ($xin_data as $key => $value) {
					$total[$num]['prefix'] = $key;
					$total[$num]['payout'] = $value['payout'];
					$num += 1;
				}


			}
			if($game == 2 || $game == 3 || $game == 4){
				if($game == 2){
					$evt_type = 'PKE'; 
				}
				else if($game == 3){
					$evt_type = 'GCR';
				}
				else if($game == 4){
					$evt_type = 'SCE';
				}
				//取得活動紀錄筆數
				$num_url = 'https://data.xingaming.net/jsp/secured/slotEventAction_querySlotEvent.htm?_dc='.time();
				$num_post = array(
					'productId'=>$in_key,
					'username'=>'',
					'redpocketid'=>'',
					'devicetype'=>'',
					'eventtype'=>$evt_type,
					'session_id'=>'',
					'currency'=>'ALL',
					'begintime'=>"$s_date 00:00:00",
					'endtime'=>"$e_date 23:59:59",
					'ordering'=>'jointime',
					'sortBy'=>'DESC',
					'pageNum'=>'25',
					'fz'=>'false',
					'page'=>'1',
					'start'=>'0',
					'limit'=>'25',
				);
				$num_data = $this->bodytool($num_url,$header,'POST',$jar,$num_post);
				$dom = new DOMDocument();
				@$dom->loadXML($num_data);
				$num_query = @$dom->getElementsByTagName('totalCount') ;
				foreach ($num_query as $key => $value) {
				   $num = $value->nodeValue;
				}


				//取得活動紀錄資料
				$data_url = 'https://data.xingaming.net/jsp/secured/slotEventAction_querySlotEvent.htm?_dc='.time();
				$data_post = array(
					'productId'=>$in_key,
					'username'=>'',
					'redpocketid'=>'',
					'devicetype'=>'',
					'eventtype'=>$evt_type,
					'session_id'=>'',
					'currency'=>'ALL',
					'begintime'=>"$s_date 00:00:00",
					'endtime'=>"$e_date 23:59:59",
					'ordering'=>'jointime',
					'sortBy'=>'DESC',
					'pageNum'=>$num,
					'fz'=>'false',
					'page'=>'1',
					'start'=>'0',
					'limit'=>$num,
				);
				$data = $this->bodytool($data_url,$header,'POST',$jar,$data_post);

				//取得站台
				$plat = $this->prefixlist();
				//print_r($plat);

				$dom = new DOMDocument();
				@$dom->loadHTML($data);
				$xpath = new DOMXpath($dom);
				//金額
				if($game == 2 || $game == 4){
					$amount_query = $xpath->query("//amount");
				}
				if($game == 3){
					$amount_query = $xpath->query("//redpcredit");
				}
				
				foreach ($amount_query as $am_key => $am_value) {
					
				   $amount[$am_key] = $am_value->nodeValue;
				}

				$username_query = $xpath->query("//username");

				foreach ($username_query as $key => $value) {
				    foreach ($plat as $p_key => $p_value) {
				        if(substr($value->nodeValue, 0, strlen($p_value)) == $p_value){	    
				            $xin_data[$p_value]['payout'] += $amount[$key];
				        }
				    }
				   
				}
			
				foreach ($xin_data as $key => $value) {
					$total[$num]['prefix'] = $key;
					$total[$num]['payout'] = $value['payout'];
					$num += 1;
				}
				// print_r($total);
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

// $x=new xin;
// $x->index('2019-08-01','2019-08-26');
