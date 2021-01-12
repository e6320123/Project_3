<?php 
// require_once('../../vendor/autoload.php');
error_reporting(0); //不報錯誤
ini_set('max_execution_time', '900');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class avia extends tools{
	public $jar;
	public function index($s_date,$e_date,$game=null){


        $e_date=new Datetime($e_date); 
        $e_date=$e_date->modify('+1 day')->format("Y-m-d");

                
        $this->jar = new CookieJar;
		//登入
		$loginurl = 'https://site.avia-gaming.com/Admin/Login';
		$header = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
		];
		$post = array(
			'username'=>'boscs',
			'password'=>'f21ae79e',
			'code'=>'',
		);

		$login = $this->bodytool($loginurl, $header, 'POST', $this->jar, $post);
		$login=json_decode($login);
		$token=$login->info->Token;

		//取得平台
		$plat_url = 'https://site.avia-gaming.com/Config/Sites';
		$header = [
			'Authorization' => $token,
		];
		$post = array();		
		$plat = $this->bodytool($plat_url, $header, 'POST', $this->jar, $post);
		$plat = json_decode($plat,true);
		//取得輸贏

		$data_url = [
			'1'=>'https://site.avia-gaming.com/Game/ESportOrder',
			'2'=>'https://site.avia-gaming.com/Game/SmartOrder',
		];
		foreach ($plat['info'] as $key => $value) {
			if($key==0){
				continue;
			}
			$data_post =[
					'1'=>[
						'PageIndex'=>'1',
						'PageSize'=>'20',
						'SiteID'=>$value['SiteID'],
						'User'=>'',
						'OrderID'=>'',
						'Match'=>'',
						'Status'=>'',
						'IP'=>'',
						'BetMoneyStart'=>'',
						'BetMoneyEnd'=>'',
						'StartAt'=>"",
						'EndAt'=>"",
						'startRewardAt'=>"$s_date 00:00:00",
						'endRewardAt'=>"$e_date 00:00:00",
					],
					'2'=>[
						'PageIndex'=>'1',
						'PageSize'=>'20',
						'SiteID'=>$value['SiteID'],
						'User'=>'',
						'Status'=>'',
						'IP'=>'',
						'Code'=>'',
						'Index'=>'',
						'StartAt'=>"",
						'EndAt'=>"",
						'ResultStartAt'=>"$s_date 00:00:00",
						'ResultEndAt'=>"$e_date 00:00:00",
					],
					
				
			]; 
			for($i=1;$i<=2;$i++){
				$url=$data_url[$i];
				$post=$data_post[$i];
				$data = $this->bodytool($url, $header, 'POST' ,$this->jar, $post);
				$data=json_decode($data,1);
				if(isset($data['info']['data']['Money'])){
					preg_match('/bos(.+)/', $value['Name'],$prefix);
					$prefix=$prefix[1];
					$payout=$data['info']['data']['Money'];

					if(isset($total[$prefix])){
						$total[$prefix]['payout']+=$payout;
					}else{
						$total[$prefix]=[
							'prefix'=>$prefix,
							'payout'=>$payout,
						];
					}
				}

			}
			
		}
		return $total;

	}


}

// $x=new avia();
// $x->index('2019-07-01','2019-07-31');