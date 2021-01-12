<?php  
class ds{

	public function index(){

		$color=new Colors;
		$result=json_decode(file_get_contents(__dir__."/../ds.log"));

		foreach($result as $k=>$v){

			$total[]=[
				'prefix'=>$k,
				'payout'=>$v,
			];
			

		}

		

       // print_r($total);exit;
		return $total;

	}

}