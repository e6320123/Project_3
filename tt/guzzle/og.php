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

class og extends tools{

    public function index($s_date,$e_date,$game=null){               //抓取og廠商的資料並回傳，傳入年月日

        $e=new Datetime($e_date);                         //轉換時區，Datetime可以把傳入的參數變成時間物件
        $ed_date=$e->modify('+1 day')->format("Y-m-d");   //modify()可以調整時間，format()為輸出的格式
     
        //登入
        $jar = new CookieJar;                     
        $url='http://www.og8888.net/Security/userlogin.aspx';
        $vurl="http://www.og8888.net/Security/SpaceCheckCode.aspx";
        $header=array(
            'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',

            'Accept-Language'=>'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
            'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
        );

        $body=$this->bodytool($url,$header,'GET',$jar,null);
        $viewresult=$this->view($body);            
        $verifydata=$this->headertool($vurl,'GET',$jar,$header,null);       
        $verifycode = $verifydata['Set-Cookie'];                //headers裡面再抓出Set-Cookie標籤存成陣列
        preg_match('/SpaceCode=(.+);/', $verifycode[0], $verify);
        //return $verify[1];
        //print_r($viewresult);exit;
        $post=array(  

            '__VIEWSTATE'=>$viewresult[0],
            '__VIEWSTATEGENERATOR'=>$viewresult[1],
            'txt_LoginName'=>'bgecs',
            'txt_UserPassword'=>'123qwe',
            'txt_CheckCode'=>$verify[1],
            'GetIP'=>'',
            'btn_Login'=>'登   录', 

        );    
        $this->bodytool($url,$header,'POST',$jar,$post);

        //捞报表
        $url2='http://www.og8888.net/Report/LoseWinReport.aspx';
        $body2=$this->bodytool($url2,$header,'GET',$jar,null);
        //print_r($body2);exit;     
        $viewresult2=$this->view($body2);
        $post2= array(    
            '__EVENTTARGET'=>'',
            '__EVENTARGUMENT'=>'',
            '__LASTFOCUS'=>'',
            '__VIEWSTATE'=>$viewresult2[0],
            '__VIEWSTATEGENERATOR'=>$viewresult2[1],
            'btn_search'=>'查询',                         
            'ddl_game'=>'0', 
            'txt_startDate'=>$s_date.' 12:00:00',          
            'txt_endDate'=>$ed_date.' 11:59:59',
            'HiddenField1'=>'0',
            'platformID'=>'0',

        );
        //print_r($post2);
        $result2=$this->bodytool($url2,$header,'POST',$jar,$post2); 
        //print_r($result2);exit;            
        $dom =new DOMDocument();                       
        @$dom->loadHTML($result2);                     
        $trs=$dom->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');

        foreach ($trs as $k => $v) {                            
            $prefix=trim($v->getElementsByTagName('td')->item(1)->nodeValue);
            ($prefix=='BGJSH') ? $prefix='BGJSS' : false ;
 
            $payout=trim($v->getElementsByTagName('td')->item(5)->nodeValue);

            if(preg_match("/bge/i",$prefix) && $payout!=0){ //
                preg_match("/bge(.+)/i",$prefix,$newprefix);
                $total[$k]=[

                    'prefix' => $newprefix[1],
                    'payout' => $payout,

                ];

            }else if(preg_match("/bg/i",$prefix) && $payout!=0){
                preg_match("/bg(.+)/i",$prefix,$newprefix);
                $total[$k]=[

                    'prefix' => $newprefix[1],
                    'payout' => $payout,

                ];
            }                 
        } 
        //print_r($total);exit;                        
        foreach($total as $b){ 

          $na[]=$b['prefix'];

        }
        array_multisort($na, SORT_ASC, $total);
  //使用foreach+array_multisort()，可以排序二維陣列的順序，在foreach中把$total內所有prefix的值整理成一個陣列$na，
  //array_multisort('排序依據','排序類型(SORT_ASC是從小到大，SORT_DESC是從大到小)'，'要進行排序的陣列')，
  //把$na從小到大排序後，再把$total的內容按照$na的順序回填             
  
        //print_r($total);exit;        
        return $total;                                    

     
    }
    public function view($body){

        $dom=new DOMDocument();
        @$dom->loadHTML($body);                             
        $__VIEWSTATE=$dom->getElementById("__VIEWSTATE")->getAttribute('value');
        $__VIEWSTATEGENERATOR=$dom->getElementById("__VIEWSTATEGENERATOR")->getAttribute('value');

        $viewresult=array(    

            $__VIEWSTATE,
            $__VIEWSTATEGENERATOR,

        );
        return $viewresult;
    }

 
 
}


//  $x=new og_guzzle();                                      
// $x->index('2019-04-01 12:00:00','2019-04-22');                                          


?>

