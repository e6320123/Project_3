<?php  
class agin{

	public function index($s_date,$e_date,$game){


		$path = dirname(dirname(__FILE__)).'\agin_tmp_'.$s_date.'_'.$e_date."\\";
		
		$line = array('da6','da7','da8','db0','db1','db2','db3','db4','db5','db6','db7','da9','dk6','ex3');

		$n = 0;
		foreach ($line as $line_key => $line_value) {
			$file_data = json_decode(file_get_contents($path.$line_value.'_tmp.log'),true); 
			if($game == 1){
				foreach ($file_data['AGIN_comp'][0]['prefix'] as $l_key => $l_value) {
					$total[$n]['prefix'] = $l_value;
					$total[$n]['payout'] = $file_data['AGIN_comp'][0]['payout'][$l_key];
					$n++;
				}
			}
			else if($game == 2){
				foreach ($file_data['AGIN_redpocket'][0]['prefix'] as $l_key => $l_value) {
					$total[$n]['prefix'] = $l_value;
					$total[$n]['payout'] = $file_data['AGIN_redpocket'][0]['payout'][$l_key];
					$n++;
				}
			}
			else if($game == 3){

				foreach ($file_data['AGIN_lanternLottery'] as $l_key => $l_value) {
					$total[$n]['prefix'] = $l_value['prefix'];
					$total[$n]['payout'] = $l_value['payout'];
					$n++;
				}
			}
			else{

				foreach ($file_data['AGIN_hunterevt'] as $l_key => $l_value) {
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

		return $total;

	}

}