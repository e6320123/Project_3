<?php
	class order {
		public $useragent 	 = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
	    public $is_login 	 = false;
	    public $cookieVerify = '';
	    public $prefix       = [];

	    function __construct() {
	    	if (!empty($this->class_name)) {
	    		$this->cookieVerify = COOLIE_PATH . DS . $this->class_name . '.txt';
	    		if (!is_file($this->cookieVerify)) {
					$file  = fopen($this->cookieVerify, "w+");
					fwrite($file,'');
					fclose($file);
				}
	    	}
	    	$this->prefix = $this->get_prefix();
    	}

    	public function curl($url='',$data='',$type='get',$header='') {
    		if ($type == 'get' && is_array($data) && sizeof($data) > 0) {
    			$url .= '?' . http_build_query($data);
    		}

	        $curl = curl_init();
	        curl_setopt($curl, CURLOPT_URL, $url);
	        curl_setopt($curl, CURLOPT_REFERER, $url);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	        curl_setopt($curl, CURLOPT_HEADER, 0);
	        curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	        if (is_array($header)) {
	        	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	        }
	        if ($this->cookieVerify) {
	        	curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieVerify);
	        	curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieVerify);
	        }
	        if ($type == 'post') {
	            curl_setopt($curl, CURLOPT_POST, count($data));
	            curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
	        }

	        $r 	= curl_exec($curl);
	        if (curl_error($curl)) $this->eprint(curl_error($curl));
	        curl_close($curl);

			$this->eprint('======================================== CURL *');
			$this->eprint('$url : ' . $url);
			$this->eprint('$data : ' . json_encode($data));
			$this->eprint('$r : ' . $r);
			$this->eprint('======================================== END *') . PHP_EOL;
	        return $r;
	    }

	    public function eprint($str){
			echo date('[Y-m-d H:i:s] ',time()) . $str . " \n";
		}

		public function to_log($name,$data){
			$fp = fopen(LOG_PATH . DS . $this->class_name . '_' . $name . '.log', 'w+');
		    fwrite($fp, $data);
		    fclose($fp);
		}

		public function to_add_log($name,$data){
			$fp = fopen(LOG_PATH . DS . $this->class_name . '_' . $name . '.log', 'a+');
		    fwrite($fp, $data);
		    fclose($fp);
		}

		public function get_prefix() {
			return ['bet','vip','j88','xxj','vns','vnn','yhh','wns','jss','b38','wei','xxp','xpp','b35','bge','v88','jjs','b65','vvn','vnt','t88','yyh','xps','js2','can','s21','df8','cna','xp2','66s','lis','g21','v21','x21','b21','xab','lvt','cnn','jjt','vss','b70','y18','bt3','bt8','ios','sss','dev','b66','b33','y89','b88','b99','daf','ppx','wyn','xh8','bb3','n88','b63','jsa','df6','zwns','yh8','j95','b96','win','j70','fun','sun','z88','vnc','amyh','yh6','6pj','xx7','mmy','b73','pj8','scc','pjc','yin','rx9','yyl','wlb','b22','jsy','b31','dzj','b68','bes','b80','vvv','ven','b77','b98','feh','b56','ver','ajs','hg8','tgo','xxc','ylg','dfc','666','w97','k88','y09','t58','qwe','dsh','bzp','zfc','hg6','demo','jin','xj8','btt','b32','b62','663','blgj','678','vbm','kts','pp1','hky','yh92','kos','t38','p58','tyc','p8j','j32','bb9','bte','svi','vn3','bd8','y68','fll','ty8','qdd','ttb','b131','ysh','tbg','v99','x9j','n13','x88','ccc','xtd','bbe','jsc','999','3368','b58','911','xg8','jjj','ttt','bb6','555','b86','222','t30','vag','333','188','789','bob','b18','111','no1','gbt','mm8','k99'];
		}
	}
