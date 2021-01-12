<?php
require_once('../vendor/autoload.php') ;

class DS 
{
	private $client ;
	private $cookieJar  ;		//多線程cookie變數
	private $concurrency = 2 ; 	//多線程
	/**
	 * @var string nodejs的安裝路徑
	 */
	private $nodejs = '';
	/**
	 * @var string ds.js的路徑
	 */
	private $dsjs = __DIR__ . '/ds.js';
	private $prefix = 'bos' ;
	private $sd = '' ;
	private $ed = '' ;
	private $results = array() ;
	// use CCOLOR ;

	function __construct() {
		//$this->picpath = dirname(dirname(__FILE__)).'/tmp/bg.gif' ;
	}

	public function process($sd,$ed)
	{	
		print_r("查詢時間 ".$sd.' ~ '.$ed,true) ;
		$sd = date('Y/m/d', strtotime($sd)) ;
		$ed = date('Y/m/d', strtotime($ed)) ;
		//$ed = date('Y/m/d', strtotime('+1 days',strtotime($ed))) ;
		$this->sd = $sd ;
		$this->ed = $ed ;
		
		$this->getNodeMiscPath();
		empty($this->nodejs) && die('未安裝nodejs或node.exe不在環境變數path內' . PHP_EOL);
		exec("{$this->nodejs} {$this->dsjs} $sd $ed", $results);
		$results = json_decode($results[0],true) ;
		foreach($results[$sd] as $row)
		{
			$site = str_replace($this->prefix,"",$row['site']) ;
			$money = str_replace("$","",$row['money']) ;
			$money = (float)str_replace(",","",$money) ;
			$this->returndata[$site] = $money ;
		}
		//print_r("完成".PHP_EOL) ;
		file_put_contents('ds.log',json_encode($this->returndata));
	}

	public function mutiprocess($sd)
	{
		$sd = date('Y/m/d', strtotime($sd)) ;
		$this->returndata = array() ;
		if(count($this->results)==0)
		{
			$this->getNodeMiscPath();
			empty($this->nodejs) && die('未安裝nodejs或node.exe不在環境變數path內' . PHP_EOL);
			exec("{$this->nodejs} {$this->dsjs} {$this->sd} {$this->ed} everyday", $results);
			$this->results = json_decode($results[0],true) ;
		}

		foreach($this->results[$sd] as $row)
		{
			$site = str_replace($this->prefix,"",$row['site']) ;
			$money = str_replace("$","",$row['money']) ;
			$money = (float)str_replace(",","",$money) ;
			$this->returndata[$site] = $money ;
		}
		$this->w_whitepurple("查詢時間 ".$sd,true) ;
		return $this->returndata ;
	}
	
	/**
	 * 取得node與ds.js的路徑(同ebet, 待整合)
	 */
	private function getNodeMiscPath() {
		switch (php_uname('s')) {
			case 'Windows NT':
				$this->nodejs = '"' . trim(shell_exec('where node 2>NUL')) . '"'; // 避免空格(Program Files)
				break;
			case 'Linux':
			default:
				$this->nodejs = trim(shell_exec('which node'));
		}
		$this->dsjs = __DIR__  . '/ds.js';
	}
}
$x= new ds;
$x->process($argv[1],$argv[2]);
?>