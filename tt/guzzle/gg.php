<?php
//require_once 'vendor/autoload.php';
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php'; 

class gg extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;
		$loginurl='https://cp3.gg137.com/admin/Index/req_login';
		$header=array(
			'Accept'=>'application/json, text/plain, */*',
			//'Cookie'=>$cookie,
		);
		$post=array(
			'username'=>'df_bos_cs1',
			'password'=>'Is3XxUslmV7Z',
		);
		$this->bodytool($loginurl,$header,'POST',$jar,$post);
		$prefixdata=$this->bodytool('https://cp3.gg137.com/admin/Loader/search_groups',$header,'GET',$jar,null);
		//print_r($prefixdata);exit;
		$prefix_tmp=json_decode($prefixdata);

		foreach($prefix_tmp->data[0]->list[0]->list as $k=>$v){

			$prefix[$k]=[
				'id'=>$v->id,
				'prefix'=>$v->name,
			];

		}

		foreach($prefix as $v){			#把站台id变索引

			$ind[$v['id']]=$v;

		}

		$reurl='https://cp3.gg137.com/admin/Report/get_channelBets';
		$post2=array(
			'draw'=>'1',
			'columns[0][data]'=>'channel',
			'columns[0][name]'=>'', 
			'columns[0][searchable]'=>true,
			'columns[0][orderable]'=> false,
			'columns[0][search][value]'=>'', 
			'columns[0][search][regex]'=>false,
			'columns[1][data]'=> 'profit',
			'columns[1][name]'=>'', 
			'columns[1][searchable]'=> true,
			'columns[1][orderable]'=> false,
			'columns[1][search][value]'=> '',
			'columns[1][search][regex]'=> false,
			'columns[2][data]'=> 'bets',
			'columns[2][name]'=>'', 
			'columns[2][searchable]'=> true,
			'columns[2][orderable]'=> false,
			'columns[2][search][value]'=>'', 
			'columns[2][search][regex]'=> false,
			'columns[3][data]'=> 'wins',
			'columns[3][name]'=>'', 
			'columns[3][searchable]'=> true,
			'columns[3][orderable]'=> false,
			'columns[3][search][value]'=> '',
			'columns[3][search][regex]'=> false,
			'columns[4][data]'=> 'rebates',
			'columns[4][name]'=>'', 
			'columns[4][searchable]'=> true,
			'columns[4][orderable]'=> false,
			'columns[4][search][value]'=>'', 
			'columns[4][search][regex]'=> false,
			'columns[5][data]'=> 'date',
			'columns[5][name]'=>'', 
			'columns[5][searchable]'=> true,
			'columns[5][orderable]'=> false,
			'columns[5][search][value]'=>'', 
			'columns[5][search][regex]'=> false,
			'start'=> 0,
			'length'=> 200,
			'search[value]'=>'', 
			'search[regex]'=> false,
			'gid'=> 0,
			'aid'=> 7,
			'cid'=> 0,
			'start_date'=> $s_date,
			'end_date'=> $e_date,
			'game_type'=> 0,
			'checks'=>'', 
		);

		$body=$this->bodytool($reurl,$header,'POST',$jar,$post2);
		$data=json_decode($body);

		//print_r($ind);exit;
		foreach($data->data as $k=>$v){
			preg_match('/bos(.+)_bs/',$ind[$v->channel]['prefix'],$prefix);
			if($v->bets-$v->wins-$v->rebates!=0){
				$total[$k]=[

					'prefix'=>$prefix[1],
					'payout'=>$v->bets-$v->wins-$v->rebates,
				];
			}

		}
		if(isset($total)){
			foreach($total as $b){  

				$na[]=$b['prefix'];

			}
			array_multisort($na, SORT_ASC, $total);
		} 
		return $total;
		// print_r($total);exit;

	}
}
// $x=new gg;
// $x->index();