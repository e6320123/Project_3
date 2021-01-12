<?php 

class ebet{

	public function index(){

		$color=new Colors;
		//print_r(file_get_contents($name));exit;
		$result=json_decode(file_get_contents(__dir__."/../ebet.log"),true);

		foreach($result as $k=>$v){

			preg_match('/BGE(.+)/i',$v['site'],$prefix);
			$total[]=[
				'prefix'=>$prefix[1],
				'payout'=>$v['money'],
			];
			

		}

		

       	
		return $total;

	}

}