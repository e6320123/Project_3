<?php

use \GuzzleHttp\Client;
use GuzzleHttp\Pool;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;


class tools{

        public $proxy='10.1.0.5:3128';
        public $pool_data;
        public function bodytool($url,$header,$method,$cookie,$post=null,$type='form_params',$query=null){								//抓取body

        if($cookie!=null){
            $client=new Client(array(       
                'cookies' => $cookie,
                'verify' => false,
                'proxy'=>$this->proxy,
            ));
        }else{

            $client=new Client(array(
                'verify'=> false,
                'proxy'=>$this->proxy,
            ));

        }

        if($query!=null){

            $res =$client->request($method,$url,[   
                
                'headers' => $header,
                "$type" => $post,
                'query' => $query,

            ]);

        }else if($post!=null){

                $res =$client->request($method,$url,[					
                    'headers' => $header,
                    "$type"=>$post,
                ]);

        }else{

            $res =$client->request($method,$url,[          
                'headers' => $header,
            ]);

        }

            $body =$res->getbody()->getcontents();	
        //echo $res->getStatusCode();			
        return $body;

        }

        public function headertool($vurl,$method,$cookie,$header,$post=null,$type='form_params',$query=null){							//抓取header

        if($cookie!=null){
            $client=new Client(array(                   
                'cookies' => $cookie,
                'verify' => false,
                'proxy'=>$this->proxy,
            ));
        }else{

            $client=new Client(array(
                'verify' => false,
                'proxy'=>$this->proxy,
            ));

        }


        if($query!=null){
            $res =$client->request($method,$vurl,[         
                'headers' => $header,
                "$type" => $post,
                'query' => $query,
            ]);

        }else if($post!=null){
            $res =$client->request($method,$vurl,[         
                'headers' => $header,
                "$type"=>$post,
            ]);

        }else{

            $res =$client->request($method,$vurl,[          
                'headers' => $header,
            ]);

        }


        $header = $res->getHeaders();						
        return $header;

        }

    public function prefixlist(){     #抓取站台列表

            $loader = new \Composer\Autoload\ClassLoader();
            $loader->addPsr4('phpseclib\\', __DIR__ . '/path/to/phpseclib2.0');
            $loader->register();

            $key = new RSA();
            if(php_uname('s')=='Windows NT'){
                $user = $this->promptAuthUserName();
                $key->loadKey(file_get_contents(getenv('userprofile')."/.ssh/id_rsa2"));
                $ssh = new SSH2('10.10.1.24');
            }else{
            $user= str_replace("\n","",shell_exec('whoami'));
            $key->loadKey(file_get_contents("/home/$user/.ssh/id_rsa"));
            $ssh = new SSH2('192.168.150.38');
            }
            
            $ssh ->setTimeout(600);
            if (!$ssh->login($user, $key)) {
                exit('Login Failed');
            }
            $result=$ssh->exec("/usr/bin/php56 /home/$user/sql/sqldata.php prefix");
            $result=json_decode($result,true);
            return $result;

    }

    public function prefixlist_bb(){     #抓取站台列表

            $loader = new \Composer\Autoload\ClassLoader();
            $loader->addPsr4('phpseclib\\', __DIR__ . '/path/to/phpseclib2.0');
            $loader->register();

            $key = new RSA();
            if(php_uname('s')=='Windows NT'){
                $user = $this->promptAuthUserName();
                $key->loadKey(file_get_contents(getenv('userprofile')."/.ssh/id_rsa2"));
            }else{
            $user= str_replace("\n","",shell_exec('whoami'));
            $key->loadKey(file_get_contents("/home/$user/.ssh/id_rsa"));
            }
            $ssh = new SSH2('192.168.150.38');
            $ssh ->setTimeout(600);
            if (!$ssh->login($user, $key)) {
                exit('Login Failed');
            }
            $result=$ssh->exec("/usr/bin/php56 /home/$user/sql/sqldata.php prefix_bb");
            $result=json_decode($result,true);
            return $result;

    }

    public function do_pool($url,$header,$cookie,$post=null,$type='form_params',$query=null){

        
        $client = new Client(['verify'=>false,'timeout'=>30,'connect_timeout'=>30,'proxy'=>$this->proxy]);

        $requests = function () use ($client,$header, $url,$cookie,$post,$type,$query){
            if($post!=null){

                foreach($post as $k => $v)
                { 
                    yield function () use ($client,$header, $url,$cookie,$v,$k,$type,$query) {

                        if($query!=null){

                            return $client->requestAsync("POST", $url,[
                                'header' => $header,
                                "$type" => $v,
                                'cookies' => $cookie,
                                'query' => $query[$k],
                            ]);

                        }else{

                            return $client->requestAsync("POST", $url,[
                                'header' => $header,
                                "$type" => $v,
                                'cookies' => $cookie,
                            ]);

                        }

                    };
                }

            }else{

                foreach($query as $k)
                { 

                    yield function () use ($client,$header,$url,$cookie,$k) {
                                            
                            if($k!=null){

                                return $client->getAsync($url,[
                                    'headers' => $header,
                                    'cookies' => $cookie,
                                    'query' => $k,
                                ]);

                            }else{

                                return $client->getAsync($url,[
                                    'headers' => $header,
                                    'cookies' => $cookie,
                                ]);

                            }
                    };
                }

            }
        };

        $pool = new Pool($client, $requests(), [

            'concurrency' => '10',
            'fulfilled' => function ($response, $index){
                //連線成功
                $this->pool_data[$index] = $response->getBody()->getContents();
                // error_log("第".$index."個請求|Success".PHP_EOL);
            },
            'rejected' => function ($reason, $index) {
                //連線失敗
                error_log("rejected $index \n");
                error_log("rejected reason: " . $reason . "\n");

            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        
        return $this->pool_data;
    }

    public function captcha($pic, $value){
        $i=imagecreatefromstring($pic);
        for ($y=0;$y<imagesy($i);$y++) {          

            for ($x=0;$x<imagesx($i);$x++) {

                $rgb = imagecolorat($i,$x,$y);
                $r = ($rgb>>16) & 0xFF;
                $g = ($rgb>>8) & 0xFF;
                $b = $rgb & 0xFF;
                if($b < $value ){          

                        echo'*';

                    }
                    else{

                        echo' ';
                    }

                }

                echo "\n";            
            }
            fwrite(STDOUT, "验证码:");
        $code = trim(fgets(STDIN));
        return $code;
    }

    /**
     * windows底下建立sshname.txt檔案, 若檔案不存在, 則要求使用者輸入auth的帳號
     * 
     * @return string 已輸入的帳號或者讀取舊的sshname.txt檔案
     */
    public function promptAuthUserName() {
        $user = '';
        $userfile = 'sshname.txt';
        if (file_exists($userfile)) {
            $user = file_get_contents($userfile);    
        } else {
            for (;;) {
                $color = new Colors();
                echo $color->colorize('請輸入您於auth(28)的帳號名稱: ', 'SUCCESS');
                $user = trim(fgets(STDIN));
                if (empty($user)) {
                    continue;
                }
                file_put_contents($userfile, $user);
                break;
            }
        }
        return $user;
    }
}
