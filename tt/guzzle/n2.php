<?php 
// require_once('../../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class n2 extends tools{
	public $client;
	public $jar;
	public $plat;

	public function index($s_date,$e_date){
		
        
		$this->client = new Client();
		$this->jar=new CookieJar;

		//取得登入頁
		$login_page_url = 'https://bem.azuritebox.com/';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$login_page = $this->bodytool($login_page_url, $header, 'GET', $this->jar, null);
		$dom = new DOMDocument();
		@$dom->loadHTML($login_page);
		$xpath = new DOMXpath($dom);

		$RequToken = $xpath->query("//input[@name='__RequestVerificationToken']");
		foreach($RequToken as $tmp_key1 => $tmp_val1){
		    $token = $tmp_val1->getAttribute('value');
		}

		//取得驗證碼
		$captcha_url = 'https://bem.azuritebox.com/Account/CaptchaImage';
		$post = array(
			'id' => 1,
		);
		$captcha = $this->bodytool($captcha_url, $header, 'POST', $this->jar, $post);
		$tmp_json = json_decode($captcha,true);
		if((string)$tmp_json['objAction'] == '-'){
		    $ans = (int)$tmp_json['objA']-(int)$tmp_json['objB'];
		  
		}
		else{
		    $ans = (int)$tmp_json['objA']+(int)$tmp_json['objB'];
		}

		//驗證驗證碼
		$verify_url = 'https://bem.azuritebox.com/Account/VerifyCaptcha';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
			'X-Requested-With' => 'XMLHttpRequest',
		];
		$post = array(
			'Captcha' => $ans,
			'EncryptAnswer' => $tmp_json['Answer'],
		);
		$verify = $this->bodytool($verify_url, $header, 'POST', $this->jar, $post);

		//登入
		$login_url = 'https://bem.azuritebox.com/';
		$post = array(
        	'__RequestVerificationToken' => $token,
            'UserName' => 'BGECS',
            'Password' => 'onWW07P8q',
            'Language' => 'zh-CN',
            'RememberMe' => 'false',
            'Captcha' => $ans,
		);
		$login = $this->bodytool($login_url, $header, 'POST', $this->jar, $post);
		
		//登入N2
		$page_url = 'https://bem.azuritebox.com/ProfitAndLossReport/Index';
		$page = $this->bodytool($page_url, $header, 'GET', $this->jar, null);
		$dom = new DOMDocument();
		@$dom->loadHTML($page);
		$xpath = new DOMXpath($dom);
		$plat_query = $xpath->query("//select[@name='MerchantId']//option");
		foreach($plat_query as $tmp_key1 => $tmp_val1){
			if($tmp_val1->getAttribute('value') != ''){

				$id[$tmp_key1] = $tmp_val1->getAttribute('value');
		    	$this->plat[$tmp_key1] = strtolower(substr($tmp_val1->nodeValue,3,strlen($tmp_val1->nodeValue)));
			}
		    
		}
		// echo '平台'.PHP_EOL;
		// print_r($this->plat);
		//取得輸贏報表



		
		$st = str_replace('/', '%2F', date('d/m/Y', strtotime($s_date)));
        $et = str_replace('/', '%2F', date('d/m/Y', strtotime("+1 day",strtotime($e_date))));

	

        $requests = function($total, $st, $et, $id){
            
            for($i = 1; $i <= $total; $i++){
                $url = 'https://bem.azuritebox.com/ProfitAndLossReport/List?FromDate='.$st.'&ToDate='.$et.'&CurrencyIds=11&MerchantIds='.$id[$i].'&StatusIds=1&HoldPercentageBaseOn=1&MerchantRecursive=false&_='.time().rand(100,999);
                yield function() use($url){
                    return $this->client->getAsync($url,[
                        'headers' => [
							'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
							'X-Requested-With' => 'XMLHttpRequest',
						],
                        'verify' => false,
                        'cookies' => $this->jar, 
                        'proxy'=>'10.1.0.5:3128',
                    ]);  
                    
                };

                
            }
        };
        
        $pool = new Pool($this->client, $requests(count($id), $st, $et, $id),[
            'concurrency' => '5',
            //連線成功
            'fulfilled' => function($response, $index){
                // echo '取得第 '.$index.'筆資料成功..'.PHP_EOL;

                $tmp_return = $response->getBody()->getContents() ;
                $dom = new DOMDocument();
                @$dom->loadHTML($tmp_return);
                $xpath = new DOMXpath($dom);

                $profit_loss = $xpath->query("//a[@id='main-ProfitOrLoss']");
                foreach($profit_loss as $tmp_key1 => $tmp_val1){
                    $this->total[$index]['prefix'] = $this->plat[$index+1];
					$this->total[$index]['payout'] = str_replace(',', '',trim($tmp_val1->nodeValue));
                }
            },
            //連線失敗
            'rejected' => function($reason, $index){
                error_log("rejected $index \n");
                error_log("rejected reason: " . $reason . "\n");
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();


		foreach($this->total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $this->total);
		//print_r($this->total);
		return $this->total;

	}

	
}

// $x=new n2;
// $x->index('2019-08-01','2019-08-01');