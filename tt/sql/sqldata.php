<?php 

$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('phpseclib\\', __DIR__ . '/path/to/phpseclib2.0');
$loader->register();
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class sqldata {

	public function index($s_date,$e_date,$name,$game=null){
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
		$result=$ssh->exec("/usr/bin/php56 /home/$user/sql/sqldata.php $s_date $e_date $name $game");
		$result=json_decode($result,true);
		return $result;

	}

	public function index_detail($s_date,$e_date,$name,$game=null,$plat,$accountcode=null){
		if($game == null){
			$game = 'nogame';
		}
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
		$result=$ssh->exec("/usr/bin/php56 /home/$user/sql/sqldata.php $s_date $e_date $name $game $plat $accountcode");
		$result=json_decode($result,true);
		return $result;
	}

	function promptAuthUserName() {
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
