<?php
	class gns extends order {
		public $url 			= 'https://krug-bo.star0ad.com';
		public $url_api 		= 'https://krugbo-gw.star0ad.com';
		public $authorization 	= '';

		public $header = [
	        'Accept: */*',
			'Accept-Encoding: gzip, deflate, br',
			'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'content-type: application/json',
			'Host: krugbo-gw.star0ad.com',
			'Origin: https://krug-bo.star0ad.com',
			'Pragma: no-cache',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
	    ];
		public $header_json = [
	        'Accept: */*',
			'Accept-Encoding: gzip, deflate, br',
			'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
			'Connection: keep-alive',
			'content-type: application/json',
			'Host: krugbo-gw.star0ad.com',
			'Origin: https://krug-bo.star0ad.com',
			'Pragma: no-cache',
			'Referer: https://krug-bo.star0ad.com/',
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
	    ];

		function __construct() {
			$this->class_name = __CLASS__;
			parent::__construct($this->class_name);
    	}

		public function _login() {
			$url 	= $this->url . '/bo-api/login';
			$data 	= [
				'userName' 	=> 'BOSCS',
				'password'	=> '125c88528540028630ed59fb94ee85995c4a49ef',
			];

			$return 	= $this->curl($url, json_encode($data), 'post', $this->header_json);

			$return_arr = json_decode($return,true);
			$this->to_log('login_msg',$return);

			if (is_array($return_arr) && $return_arr['success'] === false) {
				$this->eprint('登入失败，请检查你的账号密码是否错误');
			}
			if (isset($return_arr['accessToken'])) {
				$this->authorization = $return_arr['accessToken'];
				$this->is_login = true;
			}
			return $this->is_login;
		}

		public function _get_data() {
			$this->_get_details();
		}

		// 取得列表
		public function _get_list() {
			if (!$this->is_login) {
				if (!$this->_login()) { $this->eprint('登入失败，请检查你的账号密码是否错误');exit; }
			}
			if (empty($this->month)) {
				$this->eprint('月份错误，请重新输入月份');exit;
			} else {
				$this->sdate = date('Y-m-', strtotime($this->year . '-' . $this->month)) . '01 00:00:00';
				$this->edate = date('Y-m-d', strtotime($this->year . '-' . ($this->month + 1) . '-01 00:00:00 -1 day')) . ' 23:59:59';
			}

			$url 	= $this->url_api . '/bo-api/reports/game-performance/';
			//时区为 UTC0
			$data 	= [
				'startDate' 	 => date("Y-m-d\TH:i:s\Z", strtotime($this->sdate . ' +4 hours')),
				'endDate'		 => date('Y-m-d\TH:i:s.999\Z', strtotime($this->edate . ' +4 hours')),
				'view'			 => 'GAME',
				'language'		 => 'zh-CN',
				'partnerUids' 	 => '17f1deca-da6a-fccd-d79b-33e55d07b9c8',
				'reportCurrency' => 'CNY',
				'viewFilter' 	 => 'M4-0087;V:1',
			];
			$this->header[] = 'Authorization: Bearer ' . $this->authorization;
			$this->header[] = 'Referer: ' . $this->url . '/bo-api/reports/game-performance?' . http_build_query($data);

			$return 	= $this->curl($url, $data, 'get',$this->header);
			$return_arr = json_decode($return,true);
			$this->to_log('get_list_'. $this->year . '-' . $this->month,$return);
		}

		// 取得列表
		public function _get_details() {
			if (!$this->is_login) {
				if (!$this->_login()) { $this->eprint('登入失败，请检查你的账号密码是否错误');exit; }
			}
			if (empty($this->month)) {
				$this->eprint('月份错误，请重新输入月份');exit;
			} else {
				$this->sdate = date('Y-m-', strtotime($this->year . '-' . $this->month)) . '01 00:00:00';
				$edate = date('Y-m-d', strtotime($this->sdate . ' +1 month'));
				$this->edate = date('Y-m-d', strtotime($edate . ' -1 day')) . ' 23:59:59';
				echo $this->sdate . "\n";
				echo $this->edate . "\n";
			}

			$url  = $this->url_api . '/bo-api/reports/game-performance/player-details';
			//时区为 UTC0
			$data = [
				'startDate' 	 => date("Y-m-d\TH:i:s\Z", strtotime($this->sdate . ' +4 hours')),
				'endDate'		 => date('Y-m-d\TH:i:s.999\Z', strtotime($this->edate . ' +4 hours')),
				'language'		 => 'zh-CN',
				'partnerUids' 	 => '17f1deca-da6a-fccd-d79b-33e55d07b9c8',
				'reportCurrency' => 'CNY',
				'currency' 		 => 'CNY',
				'gameId' 	 	 => 'M4-0087;V:1',
			];
			$this->header[] = 'Authorization: Bearer ' . $this->authorization;
			$this->header[] = 'Referer: ' . $this->url . '/bo-api/reports/game-performance?' . http_build_query($data);

			$return 	= $this->curl($url, $data, 'get',$this->header);
			$return_arr = json_decode($return,true);
			$order_data = [];
			if (is_array($return_arr)) {
				$regex = "#^(companyprefix|^" . implode("|^", $this->prefix) . ")#is";
				foreach ($return_arr['data']['payload'] as $key => $value) {
					if (preg_match($regex, $value['playerId'], $match)) {
						if (!isset($order_data[$match[1]])) {
							$order_data[$match[1]] = ['bet' => 0,'win' => 0,];
						}
						$order_data[$match[1]]['bet'] += floatval($value['reportCurrencyBetAmount']);
						$order_data[$match[1]]['win'] += floatval($value['reportCurrencyWinLoss']);
					} else {
						$this->eprint($value['playerId'] . '找不到对应前缀');
					}
				}
				if (sizeof($order_data) > 0) {
					$order_str   = '';
					$order_total = ['bet' => 0,'win' => 0,];
					$this->to_log('_order_data_'. $this->year . '-' . $this->month,$order_str);
					$order_str 	  = '前缀	下注金额	赢奖损失(公司)	jackpot抽水' . PHP_EOL;
					$this->to_add_log('_order_data_'. $this->year . '-' . $this->month,$order_str);
					foreach ($order_data as $key => $value) {
						$order_total['bet'] += floatval($value['bet']);
						$order_total['win'] += floatval($value['win']);
						$order_str = $key . "	" . $value['bet'] . "	" . $value['win'] . "	" . ($value['bet'] * 0.02) . PHP_EOL;
						$this->to_add_log('_order_data_'. $this->year . '-' . $this->month,$order_str);
					}
					$order_str = PHP_EOL . "total	{$order_total['bet']}	{$order_total['win']}	" . ($order_total['bet'] * 0.02);
					$this->to_add_log('_order_data_'. $this->year . '-' . $this->month,$order_str);

					$this->to_excel($order_data);
				}
			} else {
				$this->eprint('资料错误，强制终止');exit;
			}
			$this->to_log('_get_details_'. $this->year . '-' . $this->month,$return);
			$this->to_log('_get_order_data_'. $this->year . '-' . $this->month,json_encode($order_data));
		}

		public function to_excel($data) {
			require_once(CLASS_PATH . '/PHPExcel/Classes/PHPExcel.php');
			require_once(CLASS_PATH . '/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php');

			$order_total = ['bet' => 0,'win' => 0,];
		    $a 			 = 2;
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("aa")->setLastModifiedBy("Stanley")->setTitle("test1")->setSubject("test2");
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('phpexcel demo');

			$excel_arr = ['A'=>'7','B'=>'11','C'=>'17','D'=>'13'];
			foreach ($excel_arr as $key => $value) {
				$objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth($value);
			}

			$excel_arr = ['A'=>'前缀','B'=>'下注金额','C'=>'赢奖损失(公司)','D'=>'Jackpot抽水'];
			foreach ($excel_arr as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue("{$key}1",$value);
			}

		    foreach ($data as $key => $value) {
		    	$order_total['bet'] += floatval($value['bet']);
		    	$order_total['win'] += floatval($value['win']);
		    	$excel_arr = ['A'=>$key,'B'=>floatval($value['bet']),'C'=>floatval($value['win']),'D'=>floatval($value['bet'] * 0.02)];
		    	foreach ($excel_arr as $k => $v) {
					$objPHPExcel->getActiveSheet()->setCellValue("{$k}{$a}",$v);
				}
		        $a++;
		    }

		    $excel_arr = ['A'=>'TOTAL','B'=>floatval($order_total['bet']),'C'=>floatval($order_total['win']),'D'=>floatval($order_total['bet'] * 0.02)];
	    	foreach ($excel_arr as $key => $value) {
				$objPHPExcel->getActiveSheet()->setCellValue($key . ($a + 1),$value);
			}

		    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		    $objWriter->save(LOG_PATH . '/GNS_' . $this->year . (strlen($this->month) == 1 ? '0' : '') .$this->month . '_彩金统计.xls');
		}
	}
?>