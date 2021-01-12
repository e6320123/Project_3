<?php  
class ag{

	public function index($s_date,$e_date,$game=null){

		$color=new Colors;


		if($game == 2){
			$path = dirname(dirname(__FILE__)).'/agin_tmp_'.$s_date.'_'.$e_date."/";
		}
		else{
			$path = dirname(dirname(__FILE__)).'/tmp_'.$s_date.'_'.$e_date."/";
		}

		$line = array('da6','da7','da8','db0','db1','db2','db3','db4','db5','db6','db7','da9','dk6','ex3');
		$n = 0;
		foreach ($line as $line_key => $line_value) {
			$file_data = json_decode(file_get_contents($path.$line_value.'_tmp.log'),true); 
			if($game == '1'){
				foreach ($file_data['AGIN_live'] as $l_key => $l_value) {
					$total[$n]['prefix'] = $l_value['prefix'];
					$total[$n]['payout'] = $l_value['payout'];
					$n++;
				}
			}
			elseif($game == '2'){
				foreach ($file_data['AGIN_HUNTER'] as $h_key => $h_value) {
					$total[$n]['prefix'] = $h_value['prefix'];
					$total[$n]['payout'] = $h_value['payout'];
					$n++;
				}
			}
			elseif($game == '3'){

				foreach ($file_data['AGIN_SPORT'] as $s_key => $s_value) {
					$total[$n]['prefix'] = $s_value['prefix'];
					$total[$n]['payout'] = $s_value['payout'];
					$n++;
				}
			}
			elseif($game == '4'){
				foreach ($file_data['AGIN_EGAME'] as $e_key => $e_value) {
					$total[$n]['prefix'] = $e_value['prefix'];
					$total[$n]['payout'] = $e_value['payout'];
					$n++;
				}
			}
			elseif($game == '5'){
				foreach ($file_data['AGIN_XIN'] as $x_key => $x_value) {
					$total[$n]['prefix'] = $x_value['prefix'];
					$total[$n]['payout'] = $x_value['payout'];
					$n++;
				}
			}
			else{
				foreach ($file_data['AGIN_YOPLAY'] as $y_key => $y_value) {
					$total[$n]['prefix'] = $y_value['prefix'];
					$total[$n]['payout'] = $y_value['payout'];
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