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

class yp extends tools{

	public function index($s_date,$e_date,$game=null){

		$sd = date('Y-m-d', strtotime($s_date));
        $ed = date('Y-m-d', strtotime($e_date));

       	$inter = array(
            'DA6' => array(
                'username' => 'YPDA6STTd4',
                'password' => '2stN2nXS',
            ),
            'DA7' => array(
                'username' => 'YPDA7Ua4NE',
                'password' => 'AXkkPY6',
            ),
            'DA8' => array(
                'username' => 'YPDA8Pz63v',
                'password' => 'DvReKF2U',
            ),
            'DA9' => array(
                'username' => 'YPDA9D4JYS',
                'password' => 'dskJZ2Bh',
            ),
            'DB0' => array(
                'username' => 'YPDB08rufq',
                'password' => 'jvM5bxFn',
            ),
            'DB1' => array(
                'username' => 'YPDB1AUCd2',
                'password' => 'QxVx2VLH',
            ),
            'DB2' => array(
                'username' => 'YPDB2b3SMv',
                'password' => 'YkLKqnfA',
            ),
            'DB3' => array(
                'username' => 'YPDB3462jn',
                'password' => '7VCympTd',
            ),
            'DB4' => array(
                'username' => 'YPDB4KHY5k',
                'password' => '5UH37ucV',
            ),
            'DB5' => array(
                'username' => 'YPDB5ew3aP',
                'password' => 'muXrva5c',
            ),
            'DB6' => array(
                'username' => 'YPDB68PcLC',
                'password' => 'x6sFcpSN',
            ),
            'DB7' => array(
                'username' => 'YPDB75GaXu',
                'password' => '5beaJbVr',
            ),
            'DK6' => array(
                'username' => 'YPDK667R6q',
                'password' => '32JhH3ed',
            ),
            'EX3' => array(
                'username' => 'YPEX3FjLTy',
                'password' => 'WJVQrwkQ',
            ),
        );
       	$num = 0;
        foreach ($inter as $in_key => $in_value) {

            $jar = new CookieJar;

			//取得登入頁面
			$login_page_url = 'https://agyp-data.yoplay.com/jsp/loginPage.htm?err=';
			$header = [
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
			];
			$login_page = $this->bodytool($login_page_url,$header,'GET',$jar,null);
			preg_match_all('/(?<=crypt.setp\x28\x22)(.*)(?=\x22)/', $login_page, $pu_k);

			//取得驗證碼
			$captcha_url = 'https://agyp-data.yoplay.com/jsp/captcha.htm';
	        $captcha_pic = $this->bodytool($captcha_url,$header,'GET',$jar,null);

	        //登入
	        $public_key = '-----BEGIN PUBLIC KEY-----'.PHP_EOL.wordwrap($pu_k[0][0], 64, "\n", true).PHP_EOL.
	                    
'-----END PUBLIC KEY-----';
			openssl_public_encrypt($in_value['username'], $usr_encrypted, $public_key);
			$usr_encrypted = base64_encode($usr_encrypted);
			openssl_public_encrypt($in_value['password'], $pas_encrypted, $public_key);
			$pas_encrypted = base64_encode($pas_encrypted);

			$login_url = 'https://agyp-data.yoplay.com/j_spring_security_check';
			$post = array(
				'captcha'=>$this->captcha($captcha_pic, 160),
				'j_username'=>$usr_encrypted,
				'j_password'=>$pas_encrypted,
			);
			$login = $this->bodytool($login_url,$header,'POST',$jar,$post);
			
			
			
			//取得免費旋轉筆數
			$num_url = 'https://agyp-data.yoplay.com/jsp/secured/promotionAction_queryPromotionMember.htm?_dc='.time();
			$num_post = array(
				'productId'=>$in_key,
                'rewardType'=>'',
                'fromTime'=>"$s_date 00:00:00",
                'toTime'=>"$e_date 23:59:59",
                'promotionCode'=>'',
                'userName'=>'',
                'likeQueryLoginName'=>'false',
                'currency'=>'',
                'sortOrder'=>'username',
                'sortBy'=>'DESC',
                'pageNum'=>'25',
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
			$data_url = 'https://agyp-data.yoplay.com/jsp/secured/promotionAction_queryPromotionMember.htm?_dc='.time();
			$data_post = array(
				'productId'=>$in_key,
				'rewardType'=>'',
				'fromTime'=>"$s_date 00:00:00",
				'toTime'=>"$e_date 23:59:59",
				'promotionCode'=>'',
				'userName'=>'',
				'likeQueryLoginName'=>'false',
				'currency'=>'',
				'sortOrder'=>'username',
				'sortBy'=>'DESC',
				'pageNum'=>$num,
				'page'=>'1',
				'start'=>'0',
			);
			$data = $this->bodytool($data_url,$header,'POST',$jar,$data_post);

			//取得站台
			$plat = $this->prefixlist();
			$xin_data;
			$dom = new DOMDocument();
            @$dom->loadHTML($data);
            $xpath = new DOMXpath($dom);
            //金額
            $amount_query = $xpath->query("//amount");
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
