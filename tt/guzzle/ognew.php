<?php 

// require_once '../vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';


class ognew extends tools{

    public function index($s_date,$e_date,$game=null) {
        //$e_date=date("Y-m-d",strtotime($s_date."+1 day"));  //加一天
    	$jar = new CookieJar;
    	$header=[
    		'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
    	];
    	$captchaurl='https://tigerapi.oriental-game.com:38888/index.php?app_url=auth/get_captcha_image&_='.strtotime('now').'&lng=zh-cn';
    	$re=$this->bodytool($captchaurl,$header,'GET',$jar);
        $login_need = json_decode($re, true);

        $token = $login_need['data']['captcha_token'];

        $img_contents = explode(',', $login_need['data']['image_base64']);
        $img_contents = base64_decode($img_contents[1]);
        $i=imagecreatefromstring($img_contents);
            for ($y=0;$y<imagesy($i);$y++) {          

                for ($x=0;$x<imagesx($i);$x++) {

                    $rgb = imagecolorat($i,$x,$y);
                    $r = ($rgb>>16) & 0xFF;
                    $g = ($rgb>>8) & 0xFF;
                    $b = $rgb & 0xFF;
                    if($b < '80' ){          

                        echo'*';

                    }
                    else{

                        echo' ';
                    }

                }

                echo "\n";            
            } 
		//file_put_contents('ognew.png', $img_contents);			
        fwrite(STDOUT, "验证码:");
        $captcha = trim(fgets(STDIN));
        $loginurl='https://tigerapi.oriental-game.com:38888/index.php?app_url=auth/login&lng=zh-cn';
        $header=[
        	'sec-fetch-dest' => 'empty',
        	'sec-fetch-mode' => 'cors',
        	'sec-fetch-site' =>'same-site',
        	'accept-encoding'=> 'gzip, deflate, br',
        	'accept-language'=> 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7'
        ];
        $post=[
        	'password'=> 'TXc5Ih28l6b5',
        	'code'=> 'mog328admin',
        	'captchaToken'=> $token,
        	'captcha_code'=> $captcha,
        ];
        $a=$this->bodytool($loginurl,$header,'POST',$this->jar,$post);
        
        $re=json_decode($a,true);
        $retoken=$re['data']['token'];
        $reurl='https://tigerapi.oriental-game.com:38888/index.php?app_url=data_repository/finance_report/search_level_three&lng=zh-cn';
        $header=[
            'content-type'=>'application/x-www-form-urlencoded; charset=UTF-8',
            'authorization'=>'Bearer '.$retoken,
        ];
        $post=[
            'draw'=>'1',
            'columns[0][data]'=>'currency',
            'columns[0][name]'=>'',
            'columns[0][searchable]'=>'false',
            'columns[0][orderable]'=>'false',
            'columns[0][search][value]'=>'',
            'columns[0][search][regex]'=>'false',
            'columns[1][data]'=>'name',
            'columns[1][name]'=>'',
            'columns[1][searchable]'=>'true',
            'columns[1][orderable]'=>'false',
            'columns[1][search][value]'=>'',
            'columns[1][search][regex]'=>'false',
            'columns[2][data]'=>'betcnt',
            'columns[2][name]'=>'',
            'columns[2][searchable]'=>'true',
            'columns[2][orderable]'=>'false',
            'columns[2][search][value]'=>'',
            'columns[2][search][regex]'=>'false',
            'columns[3][data]'=>'bettingamount',
            'columns[3][name]'=>'',
            'columns[3][searchable]'=>'false',
            'columns[3][orderable]'=>'false',
            'columns[3][search][value]'=>'',
            'columns[3][search][regex]'=>'false',
            'columns[4][data]'=>'winloseamount',
            'columns[4][name]'=>'',
            'columns[4][searchable]'=>'false',
            'columns[4][orderable]'=>'false',
            'columns[4][search][value]'=>'',
            'columns[4][search][regex]'=>'false',
            'columns[5][data]'=>'validbet',
            'columns[5][name]'=>'',
            'columns[5][searchable]'=>'false',
            'columns[5][orderable]'=>'false',
            'columns[5][search][value]'=>'',
            'columns[5][search][regex]'=>'false',
            'order[0][column]'=>'0',
            'order[0][dir]'=>'desc',
            'start'=>'0',
            'length'=>'1000',
            'search[value]'=>'',
            'search[regex]'=>'false',
            'date_range'=>$s_date." | ".$e_date,
            'currency'=>'CNY',
            'name'=>'mog328',
        ];
        $report=$this->bodytool($reurl,$header,'POST',$this->jar,$post);
        $report=json_decode($report,1);
        foreach($report['data'] as $k => $v){
            $prefix=$v['name'];
            preg_match('/mog328bos(.+)/', $prefix,$prefix);
            $payout=$v['winloseamount'];
            $total[]=[
                'prefix'=>$prefix[1],
                'payout'=>$payout,
            ];
        }

        return $total;



        
    }

}


// $og = new ognew;
// $og->index();