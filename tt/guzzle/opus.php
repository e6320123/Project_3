<?php
//require_once('../../vendor/autoload.php');
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
require_once 'class.tools.php';

class opus extends tools{

	public function index($s_date,$e_date,$game=null){

		$jar=new CookieJar;
		$prefixlist=$this->prefixlist();
		if($game == 1){//真人
			$e_date=new DateTime($e_date);
			$e_date=$e_date->format("d-M-Y");
			$s_date=new DateTime($s_date);
			$s_date=$s_date->format("d-M-Y");
			$vurl='http://opsofficeld.opus-sports.com/images/captcha/Captcha.ashx';
			$body=$this->bodytool($vurl,null,'GET',$jar,null);

			$loginurl='http://opsofficeld.opus-sports.com/index.aspx?ReturnUrl=%2f';
			$header=array(

				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
				'Accept-Encoding' => 'gzip, deflate',
				'Accept-Language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
				'Cache-Control' => 'max-age=0',
				'Connection' => 'keep-alive',
				'Content-Length' => '442',
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Host' => 'opsofficeld.opus-sports.com',
				'Origin' => 'http://opsofficeld.opus-sports.com',
				'Referer' => 'http://opsofficeld.opus-sports.com/index.aspx?ReturnUrl=%2f',
				'Upgrade-Insecure-Requests' => '1',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',

			);
			$post=array(

				'__VIEWSTATE' => '/wEPDwUKLTg5NDk2NzQzNWRkSyKYQNLlS/A4gZ/FzuK5ZrFxHV4=',
				'__VIEWSTATEGENERATOR' => '90059987',
				'__EVENTVALIDATION' => '/wEdAAgOjbpZ3H0tIlOl2oUFdnfO1aYiqeF11PmRrPJdNVwzNOEZhMhQPmqENybRVDB+DqwO7ijHwbFGg3dk+ncw4k8rVK7BrRAtEiqu9nGFEI+jB3Y2+Mc6SrnAqio3oCKbxYaZf5MUp2golT/J9ICuDcwjop4oRunf14dz2Zt2+QKDEHa3jQbkiU0B/hQLy5lsMFulGZZM',
				'rdoLang' => 'zh-CN',
				'txtUsername' => 'bgcaiwu',
				'txtPassword' => 'oy03hsdm',
				'txtCaptcha' => $this->getcaptcha($body,40,$game),
				'btnLogin' => ' 登入 ',

			);
			$getsid=$this->headertool($loginurl,'POST',$jar,$header,$post);
			$sid = $getsid['Set-Cookie']['4'];
			preg_match('/s=(.*);/i', $sid, $sid);

			$reurl='http://opsofficeld.opus-sports.com/MultiComp/ajaxpro/Mgmt.MultiComp.Web.Report.WinLose,Mgmt.MultiComp.Web.ashx';
			$header2=array(
				'Accept' => '*/*',
				'Accept-Encoding' => 'gzip, deflate',
				'Accept-Language' => 'zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7',
				'X-AjaxPro-Method' => 'OnSelect',
			);
			$post2=[
				
					'MySelectEnt'=>[

						'Agent' => 'DFG_CASH_RMB',
						'Display' => '200',
						'Error' => '',
						'FileFullName' => '',
						'FileName' => '',
						'FromDate' => $s_date,
						'GameGroup' => 'ld',
						'GameID' => '0',
						'ListDetail' => '',
						'ListOperator' => '',
						'ListSummary' => '',
						'Pages' => '1',
						'PagesDtl' => '1',
						'PartnerComps' => '305F772211BE347D8667B74AD6A7BD8C3E168C76D0360D51D47EFEF41BDB342CA77F9E1E792F1D99',
						'Records' => '0',
						'RecordsDtl' => '0',
						'ReportType' => 'Member',
						'ToDate' => $e_date,
						'UplineTree' => '',
						'__type' => 'Mgmt.MultiComp.Web.Report.WinLoseEnt, Mgmt.MultiComp.Web, Version=1.0.0.0, Culture=neutral, PublicKeyToken=null',
					],
				
			];

			$query=[

				'ver'=>'1.3',
				'appid'=>'F43D9101E96522BF',
				'sid'=>$sid[1],
			];
			

			$report=$this->bodytool($reurl,$header2,'POST',$jar,$post2,'json',$query);
			preg_match("/(.*);/i", $report, $report);
			$a=json_decode($report[1],true);
			$b = explode("D~BE_" ,$a['ListDetail']);
			foreach($prefixlist as $k => $v){

				foreach ($b as $key => $bv) {

					if($bv)
					{
						$c = explode("~" ,$bv);
						$prefix = substr($c[0],0,strlen($v));
						if($prefix !='SGD' && $prefix != 'SGI' && $prefix != 'GGB' && $prefix != 'SGB')
						{
							if($prefix == $v)
							{
								if(!isset($result[$v])){
									$result[$v] = 
									[
										'prefix' =>$prefix,
										'payout' =>trim($c[4]),
									];
								}else{
									$result[$v]['payout']+=trim($c[4]);
								}
							}
							
						}
					}
				}
			}
		}
		else{//体育
			$loginurl='http://opsoffice.opus-sports.com/Credential/Login.aspx?ReturnUrl=%2fCaptcha.ashx%3f';
			$header=array(
				'Accept' => 'Accepta',
			);
			$loginpage=$this->bodytool($loginurl,$header,'GET',$jar);
			$src_form_dom = new DOMDocument;
			@$src_form_dom->loadHTML($loginpage);
			$src = $src_form_dom->getElementById('imgCaptcha')->getAttribute('src');
			$__VIEWSTATE = $src_form_dom->getElementById('__VIEWSTATE')->getAttribute('value');
			$__VIEWSTATEGENERATOR = $src_form_dom->getElementById('__VIEWSTATEGENERATOR')->getAttribute('value');
			$__EVENTVALIDATION = $src_form_dom->getElementById('__EVENTVALIDATION')->getAttribute('value');

			$vurl='http://opsoffice.opus-sports.com/Credential/'.$src;
			$body=$this->bodytool($vurl,null,'GET',$jar);

			$login_formurl='http://opsoffice.opus-sports.com/Credential/Login.aspx';
			$header2=array(

				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
				'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36',

			);
			$post=array(

				'__VIEWSTATE' => $__VIEWSTATE,
				'__VIEWSTATEGENERATOR' => $__VIEWSTATEGENERATOR,
				'__EVENTVALIDATION' => $__EVENTVALIDATION,
				'rdlLang' => 'en-US',
				'txtUsername' => 'DFCAIWU',
				'txtPassword' => 'PASSWORD123',
				'txtAccessCode' => $this->getcaptcha($body,40,$game),
				'btnLogin' => 'btnLogina',

			);
			$this->bodytool($login_formurl,$header2,'POST',$jar,$post);
			


			#登入结束
			
			$reurl='http://opsoffice.opus-sports.com/Pages/Report/WinLossMemberNew.aspx?menu=AF13F68D63ADF6F5';
			$repage=$this->bodytool($reurl,$header2,'GET',$jar);
			$report_form_dom = new DOMDocument;
			@$report_form_dom->loadHTML($repage);
			$r__VIEWSTATE = $report_form_dom->getElementById('__VIEWSTATE')->getAttribute('value');
			$r__VIEWSTATEGENERATOR = $report_form_dom->getElementById('__VIEWSTATEGENERATOR')->getAttribute('value');
			$r__EVENTVALIDATION = $report_form_dom->getElementById('__EVENTVALIDATION')->getAttribute('value');

			$nowpage=1;
			$recursive=function($nowpage) use (&$return_data,&$recursive,&$a,&$b,&$c,&$d,$client,$s_date,$e_date,$header2,$r__VIEWSTATE,$r__VIEWSTATEGENERATOR,$r__EVENTVALIDATION,$jar){

				try{
					
					$reurl='http://opsoffice.opus-sports.com/Pages/Report/WinLossMemberNew.aspx?menu=AF13F68D63ADF6F5';

					if($nowpage==1){

						$post2=array(

							'ctl00$mainScriptManager' => 'ctl00$MainContent$upSelect|ctl00$MainContent$btnSubmit',
							'__EVENTTARGET' => '',
							'__EVENTARGUMENT' => '',
							'__LASTFOCUS' => '',
							'__VIEWSTATE' => $r__VIEWSTATE,
							'__VIEWSTATEGENERATOR' => $r__VIEWSTATEGENERATOR,
							'__EVENTVALIDATION' =>$r__EVENTVALIDATION ,
							'ctl00$MainContent$ddlDate' => '0',
							'ctl00$MainContent$txtFromDate' => $s_date,
							'ctl00$MainContent$txtToDate' => $e_date,
							'ctl00$MainContent$ddlCurrency' => 'All',
							'ctl00$MainContent$chkConvertToRMB' => 'on',
							'ctl00$MainContent$ucPaging01$ddlCurPage' => $nowpage,
							'ctl00$MainContent$ucPaging01$ddlPageSize' => '200',
							'ctl00$MainContent$_PB_Currency' => '',
							'ctl00$MainContent$_PB_BaseCurr' => '',
							'ctl00$MainContent$_PB_From' => '',
							'ctl00$MainContent$_PB_To' => '',
							'ctl00$MainContent$_PB_UserName' => '',
							'ctl00$MainContent$_PB_ReportType' => '',
							'ctl00$MainContent$_PB_Show_Menu' => 'N',
							'__ASYNCPOST' => 'true',
						);
					}else{

						$post2=array(

							'ctl00$mainScriptManager' => 'ctl00$MainContent$upSelect|ctl00$MainContent$btnSubmit',
							'__EVENTTARGET' => '',
							'__EVENTARGUMENT' => '',
							'__LASTFOCUS' => '',
							'__VIEWSTATE' => $a[1],
							'__VIEWSTATEGENERATOR' => $b[1],
							'__PREVIOUSPAGE'=> $c[1],
							'__EVENTVALIDATION' =>$d[1] ,
							'ctl00$MainContent$ddlDate' => '0',
							'ctl00$MainContent$txtFromDate' => $s_date,
							'ctl00$MainContent$txtToDate' => $e_date,
							'ctl00$MainContent$ddlCurrency' => 'All',
							'ctl00$MainContent$chkConvertToRMB' => 'on',
							'ctl00$MainContent$ucPaging01$ddlCurPage' => $nowpage,
							'ctl00$MainContent$ucPaging01$ddlPageSize' => '200',
							'ctl00$MainContent$_PB_Currency' => '',
							'ctl00$MainContent$_PB_BaseCurr' => '',
							'ctl00$MainContent$_PB_From' => '',
							'ctl00$MainContent$_PB_To' => '',
							'ctl00$MainContent$_PB_UserName' => '',
							'ctl00$MainContent$_PB_ReportType' => '',
							'ctl00$MainContent$_PB_Show_Menu' => 'N',
							'__ASYNCPOST' => 'true',

						);
					}
					
					$report=$this->bodytool($reurl,$header2,'POST',$jar,$post2);
					$dom=new DOMDocument();
					@$dom->loadHTML($report);
					$table = $dom->getElementsByTagName("table")->item(2);

					if($table!=null){

						$trs = $table->getElementsByTagName("tr");
						foreach ($trs as $key => $tr) {

							@$prefix = $tr->getElementsByTagName("td")->item(1)->nodeValue;//代理商
							@$payout = $tr->getElementsByTagName("td")->item(3)->nodeValue;//損益
						    // preg_match("/BE_(.{3})/i",$prefix,$Prefix_match);
						    
								$tmp_data[]=
								[

									'prefix' => trim($prefix),
									'payout' =>trim(str_replace(',','', $payout)),
									
								];

						}

					}
					$nowpage++;
					if(isset($tmp_data)){

						preg_match("/__VIEWSTATE\|(.*)\|[0-9]\|hiddenField\|__VIEWSTATEGENERATOR/",$report,$a);
						preg_match("/__VIEWSTATEGENERATOR\|(.*)\|[0-9]*\|hiddenField\|__PREVIOUSPAGE\|/",$report,$b);
						preg_match("/__PREVIOUSPAGE\|(.*)\|[0-9]*\|hiddenField\|__EVENTVALIDATION\|/",$report,$c);
						preg_match("/__EVENTVALIDATION\|(.*)\|[0-9]*\|asyncPostBackControlIDs/",$report,$d);
						foreach($tmp_data as $t){

							$return_data[]=$t;

						}
						return $recursive($nowpage);

					}

				} catch (\Exception $e) {
			        print_r($e->getmessage());
			        exit;
		      	}
		      	return $return_data;

			};


			$total=$recursive($nowpage);
			foreach($prefixlist as $k => $v){

				$n=strlen($v);
				foreach($total as $s => $t){
					
					preg_match("/BE_(.{".$n."})/",$t['prefix'],$newprefix);


					if(strpos($newprefix[1],$v)!==false){

						if(isset($su[$v])) {

							$su[$v]['payout'] += $t['payout'];
							
						}
						else{
							$su[$v]=[
								'prefix'=>$v,
								'payout'=>$t['payout'],
							];
						}

					}

				}

			}

			$result=array_values($su);
			array_multisort($su, SORT_ASC, $result);
		}
		
        return $result;
	}


	public function getcaptcha($img, $value,$game){
	   	$img=imagecreatefromstring($img);

		for ($y=0;$y<imagesy($img);$y++) {          

		    for ($x=0;$x<imagesx($img);$x++) {

		        $rgb = imagecolorat($img,$x,$y);
		        $r = ($rgb>>16) & 0xFF;
		        $g = ($rgb>>8) & 0xFF;
		        $b = $rgb & 0xFF;
		        if($b < $value ){
		        	$im_data[$x][$y] = '*';          
	                //echo'*';

	            }
	            else{
	            	$im_data[$x][$y] = '-';
	                //echo'-';
	            }
		    }
		   	//echo "\n";            
		}
	    
		if($game == 1){
			//判斷數字起始點
			$key_width=11; //數字寬
			$key_heigh=14; //數字長
			$key_n=154; //一個數字的總數

			$start_i = 11;
			$max_i = 3;
			$start_j = 4;

			$key = array(
				'----***-------*****-----*----*----**-----*---*------*---*------*--*-------*--*------*---*------**--*------*---*-----*----**---**-----*****-------***------' => '0',
				'-----------------*--------***-------*-*----------*----------*----------*----------*---------*----------*----------*----------*----------*---------*-------' => '1',
				'---****------******-----*----*----*---------------------------*---------**--------**--------*---------*--------**---------*---------*******----*******----' => '2',
				'---****------******-----*----*---------------------*-------***--------***----------**----------*----------*---*------*---*-----*-----******------***------' => '3',
				'-------*---------**--------***--------*-*------**-*-------*--*------*---*-----*----*----*----------********---********--------*----------*----------------' => '4',
				'---******-----******----*----------*------------***-----*******----*-----*----------*----------**---------*---*------*---**----*-----******------***------' => '5',
				'----***-------*****-----**----*----*---------*--**------******-----**---**---**-----*---*------*---*------*---*------*----*----*-----******------***------' => '6',
				'-********---********---------*---------*----------*---------*---------*----------*---------*----------*--------------------*----------*----------*--------' => '7',
				'---****------******-----*----**----------*----*----*------****------*****-----*----**---*------*---*------*---*------*---**----*-----******------***------' => '8',
				'---***-------*****-----**----*----*-----*----*-----*----*-----*----*-----*----*----**----*****-*-----***-**---------*----*----**----******------***-------' => '9',

			);


		}
	    else{
	    	//判斷數字起始點
	    	$key_width=10; //數字寬
	    	$key_heigh=14; //數字長
	    	$key_n=140; //一個數字的總數

	    	$start_i = 12;
	    	$max_i = 6;
	    	$start_j = 4;

	    	$key = array(
	    		'----------*----****-*---*******--**---**--**-----*--**----**-**-----*--*------*--*------*-**-----*--*------*--*-----*---**---**---******----' => '0',
	    		'-----------------*--------**------***------****---------*--------*---------*---------*--------*---------*---------*--------*------******----' => '1',
	    		'-----****----******---------**--------*---------*--------*--------**-------*--------*--------*-------**-------*---------*******---*******---' => '2',
	    		'---------------****----*******--------**--------**--------*--------*------**--------****--------**---------*--------*--------**---******---*' => '3',
	    		'--------**-------***------*-*------*--*----*---*----*----*---*-----*--*********-*********-------*--------*---------*---------*---*----*-----' => '4',
	    		'----******----******---*---------*---------*--------*****-----******---------*---------*---------*--------*--------**---******----****------' => '5',
	    		'*------****----*****----**-------*--------*---------******---********--*-----**-**-----**-*------*--*------*--**----*---******-----****-----' => '6',
	    		'-------------*******---*******---------*---------*--------*--------*--------*--------*--------*--------**--------*--------*--------*--------' => '7',
	    		'-----****-----******---*-----*--*------*--*-----*---**---------****-----*--***---*-----*--*------*--*------*--*----**---******-----****-----' => '8',
	    		'---------------****-----*****----*----**--*------*--*------*-**-----*--**-----*--********---****-*---------*--------*-------**----*****-----' => '9',

	    	);
	    }

	    $w = 0;
	    $w_t = 0;
	    $k = 1;
	    $h_h = 0;

	    for($i=$start_i; $i<imagesx($img)-$max_i; $i++){ //width
	        for($j=$start_j; $j<imagesy($img); $j++){ //height
	                if($w_t==0){
	                    $word[$k]['width_s']=$i; //數字開頭位於圖片的寬
	                    $word[$k]['width_e']=$i+$key_width-1;
	                    $w_t = 1;
	                    $w = $i;
	                }
	                if($h_h == 0){
	                    $h_h = $j;
	                }
	                if($h_h >= $j){
	                    $h_h = $j;
	                    $word[$k]['heigh_h'] = $h_h;
	                    $word[$k]['heigh_l'] = $h_h + ($key_heigh-1);
	                }
	        }
	        if($i == ($w+($key_width-1))){
	            $w_t = 0;
	            $k += 1;
	            $h_h = 0;
	        }
	    }

	    for($i = 1; $i <= count($word); $i++){
	        $word[$i]['key'] = '';
	        for($j = $word[$i]['heigh_h']; $j <= $word[$i]['heigh_l']; $j++){   
	            for($k = $word[$i]['width_s']; $k <= $word[$i]['width_e']; $k++){
	                $word[$i]['key'] = $word[$i]['key'].$im_data[$k][$j];
	            }
	        }
	    }

	    

	    $get_captcha='';
	    for($i = 1; $i <= count($word); $i++){
	    	$percent = 0;
	    	$num = 0;
	        foreach($key as $tmp_key1 => $tmp_val1){
	        	similar_text($word[$i]['key'], $tmp_key1, $tmp_percent);
	        	if($percent < $tmp_percent){
	        		$percent = $tmp_percent;
	        		$num = $tmp_val1;
	        	}
	            
	        }
	        $get_captcha = $get_captcha.$num;
	    }
	    //echo $get_captcha;
	    return $get_captcha;
	       
	}

}
// $x=new opus;
// $x->index('01-Jul-2019','02-Jul-2019');