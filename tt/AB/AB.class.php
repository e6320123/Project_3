<?php
require_once('../vendor/autoload.php') ;

class AB
{

	/**
	 * @var string nodejs的安裝路徑
	 */
	private $nodejs = '';
	/**
	 * @var string ds.js的路徑
	 */
	private $js = __DIR__ . '/index.js';

	// use CCOLOR ;

	function __construct() {
		//$this->picpath = dirname(dirname(__FILE__)).'/tmp/bg.gif' ;
	}

	public function process($sd,$ed,$game)
	{	
		print_r("查詢時間 ".$sd.' ~ '.$ed,true) ;
		
		$sd = date('Y-m-d', strtotime($sd)) ;
		$ed = date('Y-m-d', strtotime($ed)) ;
		$line = array('bos','xxp');
		//$line = array('bos');
		if($game == 0){
			$path = dirname(__FILE__).'\ab_tmp_'.$sd.'_'.$ed."\\";
		}


		$recursive = function() use(&$line, &$path, &$sd, &$ed, &$game, &$recursive){
			$n = 0; 
			$no = 0;  //不存在log檔的數量
			foreach ($line as $line_key => $line_value) {
				if (!file_exists($path.$line_value.'_tmp.log')){
					$no += 1;
				}
			}
			if($no == 0){
				echo "取得全部log";
				return;
				
			}
			else{
				$this->getNodeMiscPath();
				empty($this->nodejs) && die('未安裝nodejs或node.exe不在環境變數path內' . PHP_EOL);
				exec("{$this->nodejs} {$this->js} $sd $ed");
				return $recursive();			
			}

		};
		
		$recursive();
		

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
		//$this->js = __DIR__  . '/index.js';
	}
}
$x= new AB;
$x->process($argv[1],$argv[2],0);
?>