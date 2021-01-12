<?php  
class ab{

	public function index($s_date,$e_date,$game=null){

		$color=new Colors;
		$path = dirname(dirname(__FILE__)).'/ab_tmp_'.$s_date.'_'.$e_date."/";

		$line = array('xxp','bos');
		$n = 0;
		foreach ($line as $line_key => $line_value) {
			$file_data = json_decode(file_get_contents($path.$line_value.'_tmp.log'),true); 
			if($game == '1'){
				foreach ($file_data['AB_live'] as $l_key => $l_value) {
					$total[$n]['prefix'] = $l_value['prefix'];
					$total[$n]['payout'] = $l_value['payout'];
					$n++;
				}
			}
		}
		

		
		foreach($total as $b){  

			$na[]=$b['prefix'];

		}
		array_multisort($na, SORT_ASC, $total);

		

       // print_r($total);exit;
		return $total;

	}

}