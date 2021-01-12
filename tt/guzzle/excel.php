<?php  
class excel{

	public function index(){

		$color=new Colors;
		fwrite(STDOUT, $color->colorize("输入厂商资料挡案名称:(把挡案放在家目录底下) ",'SUCCESS'));
		$name =trim(fgets(STDIN));
		//print_r(file_get_contents($name));exit;
		preg_match_all("/(.*),(.*)/",file_get_contents($name),$result);


		foreach($result[1] as $k=>$v){

			if($v=='bosjsh'){
				$v='bosjss';
			}

			if($result[2][$k]!=0 && $k%2==0){

				$total[]=[

					'prefix'=>$v,
					'payout'=>$result[2][$k],
					
				];
				
			}
			

		}

		foreach($total as $v) {						#先用站台名称作索引把重复站的帐务相加，再用array_value()把索引变成数字

		    if(isset($su[$v['prefix']])) {

				$su[$v['prefix']]['payout'] += $v['payout'];
				
			}
			else{
				$su[$v['prefix']]=$v;
			}
			
		}

		foreach($su as $b){  

         	$na[]=$b['prefix'];

        }
        array_multisort($na, SORT_ASC, $su);
        $result2=array_values($su);
        //print_r($result2);exit;
		return $result2;

	}

}
// $x=new imone;
// $x->index();