<?php
include("order.class.php");
include("gns.class.php");
	// set_time_limit(0);
	// ini_set('memory_limit','2048M');
	define('DS'				,DIRECTORY_SEPARATOR );
	define('ORDER_PATH' 	,dirname(__FILE__));
	define('HOME_PATH'  	,dirname(__FILE__));
	define('CLASS_PATH' 	,ORDER_PATH . DS . 'class');
	define('LOG_PATH'		,ORDER_PATH . DS . 'log');
	define('COOLIE_PATH'	,ORDER_PATH . DS . 'cookie');
	define('EXCEL_PATH' 	,CLASS_PATH . DS . 'PHPExcel' . DS . 'Classes');

	echo_msg('----------------当前路径配置');
	echo_msg('ORDER_PATH  : ' . ORDER_PATH);
	echo_msg('CLASS_PATH  : ' . CLASS_PATH);
	echo_msg('LOG_PATH    : ' . LOG_PATH);
	echo_msg('COOLIE_PATH : ' . COOLIE_PATH);
	echo_msg('EXCEL_PATH  : ' . EXCEL_PATH);
	foreach ([CLASS_PATH,LOG_PATH,COOLIE_PATH] as $key => $value) {
		if (!is_dir($value)) { mkdir($value , 0755, true); }
	}

	function __autoload($classname){
		$str_arr = explode('_', $classname);
		if ($str_arr[0] == 'PHPExcel') {
			$path    = EXCEL_PATH;
			foreach ($str_arr as $key => $value) { $path .= DS . $value; }
			require_once($path . ".php");
		} else {
			require_once(CLASS_PATH . DS . $classname . ".class.php");
		}
	}

	// echo_msg('配置正确请输入任意键继续');
	// $brack_msg = fopen ("php://stdin","r");
	// echo_msg(trim(fgets($brack_msg)));
	// fclose($brack_msg);

	$file_name_arr 	= [];
	$data_sheet 	= opendir(CLASS_PATH);
	while ($file_name = readdir($data_sheet)) {
		if (!in_array($file_name,['.','..','order.class.php'])) { $file_name_arr[] = strtoupper(str_replace('.class.php','',$file_name)); }
	}
	closedir ($data_sheet);

	// echo_msg('请输入要执行的程式，当前可执行的 : ' . implode($file_name_arr,','));
	// $brack_msg  = fopen ("php://stdin","r");
	// $class_name = trim(fgets($brack_msg));
	// fclose($brack_msg);
	$class_name = 'GNS';

	// if (empty($class_name)) {
	// 	echo_msg('未输入任何东西，强制停止');exit;
	// } elseif (!in_array($class_name,$file_name_arr)) {
	// 	echo_msg('输入不存在的东西，强制停止');exit;
	// } else {
	// 	$class_name = strtolower($class_name);
	// }

	$class = NEW $class_name;

	echo_msg('请输入要执行月份');
	$brack_msg  	= fopen ("php://stdin","r");
	$class->month 	= intval(trim(fgets($brack_msg)));
	fclose($brack_msg);

	if (empty($class->month) || $class->month > 12) {
		echo_msg('输入不存在的月份，强制停止');exit;
	} else {
		$class->year = date('Y',strtotime(date('Y-m-d H:i:s') . ($class->month == '12' && date('m') == '01' ? ' -1 year' : '')));
	}

	$class->_get_data();
	function echo_msg($msg,$show_time=false) {
		echo ($show_time ? '[' . date('Y-m-d H:i:s') . ']' : '') . $msg . PHP_EOL;
	}
