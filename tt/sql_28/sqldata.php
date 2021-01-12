 <?php

class sqldata
{
    public function index($s_date, $e_date, $name, $game = null, $plat = null, $accountcode = null)
    {
        //獲取各站的站台名稱及輸贏並回傳
        if ($plat != null) {
            //獲得各站的資料
            $u = $this->prefix($plat);

            //到各站的db內去撈資料
            foreach ($u as $key => $data) {
                $host = $data['dbip']; //先宣告登入db的各種資料
                $db = $data['dbprefix'];
                $user = $data['dbuser'];
                $pass = $data['dbpass'];
                $prefix = $data['Prefix'];
                $funct = $name . '_detail';
                $sqltotal = $this->$funct(
                    $host,
                    $db,
                    $user,
                    $pass,
                    $s_date . ' 00:00:00',
                    $e_date . ' 23:59:59',
                    $accountcode
                );
            }
        } else {
            //獲得各站的資料
            $u = $this->prefix();

            //到各站的db內去撈資料
            foreach ($u as $key => $data) {
                $host = $data['dbip']; //先宣告登入db的各種資料
                $db = $data['dbprefix'];
                $user = $data['dbuser'];
                $pass = $data['dbpass'];
                $prefix = $data['Prefix'];
                $sqldata = $this->$name(
                    $host,
                    $db,
                    $user,
                    $pass,
                    $s_date . ' 00:00:00',
                    $e_date . ' 23:59:59',
                    $game
                );

                if ($sqldata != 0) {
                    //如果站台總輸贏不等於0就把他寫入$sqltotal這個陣列裡

                    $sqltotal[$key] = [
                        'prefix' => $prefix, //印出站台
                        'payout' => $sqldata
                    ];
                }
            }
            $name == 'delete' ? exit() : false;
            //print_r($sqltotal);exit;

            //整理撈出的資料
            foreach ($sqltotal as $b) {
                $na[] = $b['prefix'];
            }
            array_multisort($na, SORT_ASC, $sqltotal);
        }

        //使用foreach+array_multisort()，可以排序二維陣列的順序，在foreach中把$sqltotal內所有prefix的值整理成一個陣列$na，
        //array_multisort('排序依據','排序類型(SORT_ASC是從小到大，SORT_DESC是從大到小)'，'要進行排序的陣列')，
        //把$na從小到大排序後，再把$sqltotal的內容按照$na的順序回填
        //echo PHP_EOL.'资料库账务捞取完毕'.PHP_EOL;
        //print_r($sqltotal);exit;
        $sqltotal = json_encode($sqltotal);
        print_r($sqltotal);
    }
    public function dbconnect($host, $db, $user, $pass, $query)
    {
        $dbms = 'mysql';
        $dbName = $db . '_intergration';
        $dsn = "$dbms:host=$host;dbname=$dbName";

        if ($host == '10.10.1.96') {
            $dbName = 'integration';
            $dsn = "$dbms:host=$host;dbname=$dbName";
        }

        try {
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->exec("SET CHARACTER SET utf8");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error!: " . $e->getMessage() . "<br/>");
        }
        $state = $dbh->query($query);
        $data = $state->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function prefix($plat = null)
    {
        //抓取各站台的db資料
        if ($plat != null) {
            $host = '10.10.1.96';
            $db = 'integration';
            $user = 'int';
            $pass = 'integration111';
            $query = "SELECT Prefix,dbuser,dbpass,dbip,webip,dbprefix FROM `Company` where status not like 'Closed' and status not like '关闭'  AND Prefix = '$plat'";
            $data = $this->dbconnect($host, $db, $user, $pass, $query);
        } else {
            $host = '10.10.1.96';
            $db = 'integration';
            $user = 'int';
            $pass = 'integration111';
            $query = "SELECT Prefix,dbuser,dbpass,dbip,webip,dbprefix FROM `Company` where status not like 'Closed' and status not like '关闭'  AND  Prefix not like 'api' AND Prefix not like 'dev' AND Prefix not like 'idn' AND Prefix not like 'bge'";
            $data = $this->dbconnect($host, $db, $user, $pass, $query);
        }

        return $data;
    }

    public function prefix_bb($plat = null)
    {
        //抓取各站台的db資料
            $host = '10.10.1.96';
            $db = 'integration';
            $user = 'int';
            $pass = 'integration111';
            $query = "SELECT Prefix,bbagent FROM `Company` where status not like 'Closed'  AND Prefix not like 'api' AND Prefix not like 'dev' AND Prefix not like 'idn' AND Prefix not like 'bge'";
            $data = $this->dbconnect($host, $db, $user, $pass, $query);
  

        return $data;
    }

    public function pp($host, $db, $user, $pass, $s_date, $e_date,$game)
    {   
        switch ($game) {
            case '1':
                $category='205';
                break;
            
            case '2':
                $category='204';
                break;
            
        }
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-4 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-4 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(win) - sum(bet) as payout FROM `p_betslips_pp` where categoryid='$category' AND endDate BETWEEN '$st_date' AND '$ed_date' and accountid!='0'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function og($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT SUM(payout) as payout FROM p_new_rpt WHERE categoryid=160 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

     public function ognew($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT SUM(payout) as payout FROM p_new_rpt WHERE categoryid=166 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function kg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(Payout) as payout FROM `p_betslips_kg` WHERE UpdateTime BETWEEN '$st_date' AND '$ed_date' AND categoryid=21";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function cq9($host, $db, $user, $pass, $s_date, $e_date)
    {
		// $query = "SELECT SUM(payout)-SUM(jackpotbet) as payout FROM p_new_rpt WHERE categoryid=210 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(if(gametype='table',win,win-bet))-SUM(IFNULL(rake,0)) as payout FROM `p_betslips_cq9`WHERE categoryid in(209,210) AND createtime BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function cb($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $s = new DateTime($s_date);
        $ed_date = $e->modify('+12 hour')->format("Y-m-d H:i:s");
        $st_date = $s->modify('+12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(amount_win) - sum(IF(status IN (2,3),amount,0)) as payout FROM `p_betslips_cb` where categoryid=245 AND updated_at BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function gg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(if(status != 10, 0, payout - bet_sum + IF(ISNULL(discount),0,discount))) as payout FROM `p_betslips_gg` WHERE categoryid in(241,242) AND log_time between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function mw($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(winMoney)-sum(playMoney) as payout FROM `p_betslips_megawin` WHERE categoryid=221 AND cdate between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function rt($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(WinLoss) as payout  FROM `p_betslips_imone_rt` WHERE categoryid=73 AND GameDate between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function imone($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=72 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(WinLoss) as payout  FROM `p_betslips_imone` WHERE categoryid=72 AND IF(IsSettled=1,EventDateTime,WagerCreationDateTime) between '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function yg($host, $db, $user, $pass, $s_date, $e_date,$game)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        if($game=='1'){
            $query = "SELECT sum(IF(`status` IN (2,3),`winMoney` + `rebateMoney` - `buyMoney`,0)) as payout FROM `p_betslips_yicaigame` WHERE categoryid in ('249','222','223') AND createTime BETWEEN '$st_date' AND '$ed_date'";
        }else if ($game=='2'){
            $query = "SELECT SUM(`WinMoney`-`Money`) AS payout FROM `p_betslips_ygwz` where categoryid=177 and CreateDateTime BETWEEN '$st_date' and '$ed_date' and `StateOfSettlement`=10";
        }
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function sg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(winLoss+betAmount)-sum(betAmount) as payout FROM `p_betslips_sg` WHERE categoryid in (120,121) AND wagerdate BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function ae($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT SUM(payout_amt) - sum(bet_amt) as payout FROM p_betslips_ae WHERE completed_at BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function pt($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(payout-bet+netAmountBonus) as payout FROM `p_betslips_pt` WHERE categoryid in (34,35) AND starttime BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    // UTC-4
    public function opus($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        if ($game == 1) {
            $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=1 AND matchdate BETWEEN '$s_date' AND '$e_date'";
            // $query = "SELECT SUM(win-bet) as payout FROM `p_betslips_opus_live` where transaction_date_time BETWEEN '$s_date' AND '$e_date'";
        } else {
            $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=4 AND matchdate BETWEEN '$s_date' AND '$e_date'";
            // $query = "SELECT sum(winlost_amount) as payout FROM `p_betslips_opus_sport` WHERE winlost_datetime BETWEEN '$s_date' AND '$e_date'";
        }
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function ky($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(Profit) as payout FROM p_betslips_ky WHERE categoryid = 238 AND GameEndTime BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function leg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(Profit) as payout FROM p_betslips_leg WHERE categoryid = 251 AND GameEndTime BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function flow($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-4 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-4 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(IF(tranType = 'GAME_WIN', amount, 0)) -
                      (sum(IF(tranType = 'ROLLBACK' AND rollbackTranType = 'GAME_WIN', amount, 0))) -
                      (sum(IF(tranType = 'GAME_BET', amount, 0)) * -1) +
                      (sum(IF(tranType = 'ROLLBACK' AND rollbackTranType = 'GAME_BET', amount, 0))) AS payout FROM `p_betslips_flow` WHERE categoryid = 190 AND `dateTime` BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }
    public function flow_detail($host, $db, $user, $pass, $s_date, $e_date,$accountcode)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-4 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-4 hour')->format("Y-m-d H:i:s");
        if(isset($accountcode)){
            $query = "SELECT id FROM `p_betslips_flow` WHERE userId = '$accountcode' and categoryid = 190 AND `dateTime` BETWEEN '$st_date' AND '$ed_date';";
        }
        else{
            $query = "SELECT userId as memberCode, sum(IF(tranType = 'GAME_WIN', amount, 0)) -
                          (sum(IF(tranType = 'ROLLBACK' AND rollbackTranType = 'GAME_WIN', amount, 0))) -
                          (sum(IF(tranType = 'GAME_BET', amount, 0)) * -1) +
                          (sum(IF(tranType = 'ROLLBACK' AND rollbackTranType = 'GAME_BET', amount, 0))) as payout FROM `p_betslips_flow` WHERE categoryid = 190 AND `dateTime` BETWEEN '$st_date' AND '$ed_date' GROUP BY userId;";
        }
        
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data;
    }
    public function avia($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(`Money`) as payout FROM p_betslips_avia WHERE categoryid = 70 AND COALESCE(RewardAt,CreateAt) between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function crown($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT Sum(Win) as payout FROM p_betslips_crown WHERE categoryid = 215 AND `Status` != 'Rejected' AND ReportDate between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function rtg2($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(win) - sum(bet) - sum(IF(ISNULL(featureContribution),0,featureContribution)) + sum(IF(ISNULL(featurePayout),0,featurePayout)) as payout FROM p_betslips_rtg2 WHERE categoryid = 236 AND gameDate between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function sa($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=196 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(ResultAmount) as payout FROM p_betslips_sa WHERE categoryid = 196 AND PayoutTime BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function apl($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(riskamt)+sum(winamt) as payout FROM p_betslips_apl WHERE (categoryid = 180 AND timestamp BETWEEN '$st_date' AND '$ed_date' AND roundstatus = 'Closed') OR (categoryid = 181 AND betclosedon BETWEEN '$st_date' AND '$ed_date' AND roundstatus = 'Closed') OR (categoryid = 182 AND betclosedon BETWEEN '$st_date' AND '$ed_date' AND roundstatus = 'Closed') OR (categoryid = 183 AND betclosedon BETWEEN '$st_date' AND '$ed_date' AND roundstatus = 'Closed')";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function vr($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT IFNULL(a.sum1,0)+IFNULL(b.sum2,0) as payout FROM (SELECT sum(if(state = '1', 0, cost - merchantPrize)) as sum1 FROM p_betslips_vr WHERE categoryid in (232,233,243,244) AND cdate between '$st_date' AND '$ed_date') as a, (SELECT sum(if(state = '1', 0, cost - playerPrize)) as sum2 FROM p_betslips_vr WHERE categoryid in (237,234) AND cdate between '$st_date' AND '$ed_date') as b;";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function bg($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        switch ($game) {
			case '1':
				$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=226 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(payment) as payout FROM p_betslips_biggame WHERE categoryid = 226 AND lastUpdateTime BETWEEN '$s_date' AND '$e_date' AND `orderstatus` in (2,3,4)";
                break;
			case '2':
				$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=229 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(win)-sum(bet) as payout FROM p_betslips_biggame_egame WHERE categoryid = 229 AND cBetTimeEst BETWEEN '$s_date' AND '$e_date' AND `status` = 2";
                break;
			case '3':
				$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=228 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(payout)+sum(jackpot) as payout FROM p_betslips_biggame_fish WHERE categoryid = 228 AND orderTime BETWEEN '$s_date' AND '$e_date'";
                break;
        }
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function tm($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout)+sum(jackpotbet) AS payout FROM p_new_rpt WHERE categoryid=145 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(`Score`) as payout FROM p_betslips_tm WHERE `Status` = '1' AND categoryid = 145 AND InsertTime between '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function tc($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(if(result='4',winAmount,0))-sum(betAmount)+sum(if(rebateAmount is null ,0,rebateAmount)) as payout FROM p_betslips_tcgaming WHERE categoryid in (185,186,187) AND actualBetAmount > 0 AND cancelType is null AND result!='16' AND drawTime between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function gns($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=220 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(total_won) - sum(total_bet) as payout FROM p_betslips_genesis WHERE categoryid = 220 AND `timestamp` between '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function zh($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-4 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-4 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(win_loss) as payout FROM p_betslips_zhonghua WHERE categoryid = 175 AND `bet_time` between '$st_date' AND '$ed_date' and accountid != '0'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function mg($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid IN (100,101) AND matchdate BETWEEN '$s_date' AND '$e_date' AND accountid != 0";
        // $query = "SELECT sum(if((category='PAYOUT' OR category='REFUND'),amount,0))-sum(if(category='WAGER',amount,0)) as payout FROM p_betslips_mg2_trans WHERE categoryid in (100,101) AND `transaction_time` between '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function bos($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");

        $query ="SELECT sum(payout - if(status=1,act_bet_money,0)) as payout FROM p_betslips_boslott WHERE categoryid = 125 AND (updatetime BETWEEN '$st_date' AND '$ed_date')";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function xj($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=105 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT SUM(returnAmount - stake) as payout FROM `p_betslips_xj` WHERE categoryid = '105' AND wagerStatus = 2 AND orderdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function xj_detail($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT memberCode, SUM(returnAmount - stake) as payout FROM `p_betslips_xj` WHERE categoryid = '105' AND wagerStatus = 2 AND orderdate BETWEEN '$s_date' AND '$e_date' GROUP BY memberCode;";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data;
    }

    public function qt($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(totalPayout-totalBet) as payout FROM p_betslips_qtech WHERE categoryid = '20' AND completed between '$st_date' AND '$ed_date' AND status in ('COMPLETED');";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function n2($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT sum(hold) as payout FROM p_betslips_n2 WHERE categoryid = 200 AND startDate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function jdb($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=155 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT SUM(total) as payout FROM p_betslips_jdb168 WHERE categoryid = 155 AND cdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function sb($host, $db, $user, $pass, $s_date, $e_date)
    {
		// $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid IN (30,31,33,38,41) AND accountid!=0 and matchdate BETWEEN '$s_date' AND '$e_date' and accountid != 0 ";
        $query = "SELECT sum(winlost_amount) as payout FROM p_betslips_sb WHERE categoryid in (33, 30, 31, 38 ,41) AND orderdate between '$s_date' AND '$e_date' AND ticket_status NOT IN ('Reject','Refund','VOID') and accountid != 0;";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function sb_detail($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT vendor_member_id as memberCode, sum(winlost_amount) as payout FROM p_betslips_sb WHERE categoryid in (33, 30, 31, 38, 41) AND orderdate between '$s_date' AND '$e_date' AND ticket_status NOT IN ('Reject','Refund','VOID') GROUP BY vendor_member_id;";
        
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data;
    }

    public function swg2($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(win - bet)-sum(jackpot_bet)+sum(jackpot_win) as payout FROM p_betslips_skywind2 WHERE categoryid = 37 AND cdate between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function gd($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) as payout FROM p_new_rpt WHERE categoryid=206 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(`WinLoss`)-sum(`BetAmount`) as payout FROM p_betslips_gd WHERE  BetResult NOT IN ('Cancel','Voided','VOIDED_BET') AND categoryid = 206 AND BetTime BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    // UTC-4
    public function ibo($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=76 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT (sum((win_gold+vgold)*-1)+sum(vgold))*-1 as payout FROM p_betslips_ibo_live WHERE categoryid=76 AND `adddate` between '$s_date' AND '$e_date' and `result` in ('W','L','N')";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function ab($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        switch ($game) {
			case '1':
				$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=22 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(payout-bet) as payout FROM p_betslips_allbet WHERE categoryid = 22 AND starttime BETWEEN '$s_date' AND '$e_date'";
                break;
			case '2':
				$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=170 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(winOrLoss+jackpotWinOrLoss) as payout FROM p_betslips_allbet_egame WHERE categoryid = 170 AND betTime BETWEEN '$s_date' AND '$e_date'";
                break;
        }
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function hb($host, $db, $user, $pass, $s_date, $e_date)
    {
		$query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=19 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(Payout-Stake) as payout FROM p_betslips_habanero WHERE categoryid = 19 AND GameStateId = '3' AND DtCompleted BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function bbevent($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT sum(Amount) as payout  FROM p_betslips_bbin_event WHERE categoryid = 67 AND CreateTime BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function ds($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query1 = "SELECT sum(IF(status=1,payout_amount,0))-sum(IF(valid_amount!=0, valid_amount, bet_amount)) as payout  FROM p_betslips_ds WHERE categoryid in (252) AND finish_at BETWEEN '$st_date' AND '$ed_date'";
        $query2 = "SELECT sum(IF(status=1,payout_amount,0))-sum(IF(valid_amount!=0, valid_amount, bet_amount)) as payout  FROM p_betslips_ds_egame WHERE categoryid in (253) AND finish_at BETWEEN '$st_date' AND '$ed_date'";
        $casino = $this->dbconnect($host, $db, $user, $pass, $query1);
        $egame = $this->dbconnect($host, $db, $user, $pass, $query2);
        $data = $casino[0]['payout'] + $egame[0]['payout'];
        return $data;
    }

    public function ebet($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(balance) as payout  FROM p_betslips_ebet WHERE categoryid = 110  AND payoutTime BETWEEN '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

	// UTC-4
    public function ag($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        switch ($game) {
            case '1':
                $game=17;
                break;
            case '2':
                $game=49;
                break;
            case '3':
                $game=47;
                break;
            case '4':
                $game=50;
                break;
            case '5':
                $game=59;
                break;
            case '6':
                $game=48;
                break;
            default:
                # code...
                break;
        }
        $query = "SELECT SUM(`payout`) as payout FROM `p_new_rpt` WHERE `matchdate` BETWEEN '$s_date' AND '$e_date' AND categoryid = '$game'";
        
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function agin($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        if ($game == 1) {
            $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 'COMP_PRIZE' AND cdate BETWEEN '$s_date' AND '$e_date'";
        }
        else if($game == 2){
            $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 'RED_POCKET' AND cdate BETWEEN '$s_date' AND '$e_date'";
        }
        else{
            $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 8 AND creationTime BETWEEN '$s_date' AND '$e_date'";
        }

        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function xin($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        if ($game == 1) {
            $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 'REDEEM' AND cdate BETWEEN '$s_date' AND '$e_date'";
        } elseif ($game == 2 || $game == 3) {
            $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 'SLOTEVENT' AND cdate BETWEEN '$s_date' AND '$e_date'";
        }

        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function yp($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        $query = "SELECT sum(transferAmount) as payout FROM `p_betslips_agin_evt` WHERE transferType = 'REWARD' AND cdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    // UTC-4
    public function cr($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=147 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(payout) as payout FROM p_betslips_cr_sport WHERE categoryid = 147 AND resultdate BETWEEN '$s_date' AND '$e_date' AND settle = '1'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function bti($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-4 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-4 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(PL) as payout FROM p_betslips_bti_sport WHERE categoryid = 148 AND `BetSettledDate` between '$st_date' AND '$ed_date' AND `Status` <> 'Opened' AND `Status` <> 'Canceled'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function kg3($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(CAST(`win_lose` AS DECIMAL(18, 5))) as payout FROM p_betslips_kg3 WHERE categoryid = 102 AND update_time between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function sbo($host, $db, $user, $pass, $s_date, $e_date,$game)
    {   
        switch ($game) {
            case '1':
                $query = "SELECT sum(payout) as payout FROM `p_new_rpt` where categoryid = 149 and matchdate BETWEEN '$s_date' and '$e_date'";
                // $query = "SELECT sum(payment) as payout FROM p_betslips_biggame WHERE categoryid = 226 AND lastUpdateTime BETWEEN '$s_date' AND '$e_date' AND `orderstatus` in (2,3,4)";
                break;
            case '2':
                $query = "SELECT SUM(payout) AS payout FROM `p_new_rpt` WHERE categoryid=154 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(win)-sum(bet) as payout FROM p_betslips_biggame_egame WHERE categoryid = 229 AND cBetTimeEst BETWEEN '$s_date' AND '$e_date' AND `status` = 2";
                break;
            case '3':
                $query = "SELECT SUM(payout) AS payout FROM `p_new_rpt` WHERE categoryid=152 AND matchdate BETWEEN '$s_date' AND '$e_date'";
                // $query = "SELECT sum(win)-sum(bet) as payout FROM p_betslips_biggame_egame WHERE categoryid = 229 AND cBetTimeEst BETWEEN '$s_date' AND '$e_date' AND `status` = 2";
                break;
        }
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }


    public function fg2($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT sum(payout) as payout FROM `p_new_rpt` where categoryid = 191 and matchdate BETWEEN '$s_date' and '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function xbb($host, $db, $user, $pass, $s_date, $e_date)
    {   
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(bet_amount)-sum(payoff) as payout FROM p_betslips_xbb WHERE categoryid = '78' AND wagers_date BETWEEN '$st_date' and '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function icg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT (sum(win)/100)-(sum(bet)/100) as payout FROM p_betslips_icg WHERE categoryid = 79 AND status in ('finish','inactive') and createdAt between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function eg($host, $db, $user, $pass, $s_date, $e_date)
    {
        $e = new DateTime($e_date);
        $ed_date = $e->modify('-12 hour')->format("Y-m-d H:i:s");
        $s = new DateTime($s_date);
        $st_date = $s->modify('-12 hour')->format("Y-m-d H:i:s");
        $query = "SELECT sum(ifnull(payout,0)/100)-sum(bet/100) as payout FROM p_betslips_eg WHERE categoryid = 77 AND  payouttime between '$st_date' AND '$ed_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

     public function bet365($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=42 AND matchdate BETWEEN '$s_date' AND '$e_date'";
        // $query = "SELECT sum(Payout-Stake) as payout FROM p_betslips_habanero WHERE categoryid = 19 AND GameStateId = '3' AND DtCompleted BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

    public function bbin($host, $db, $user, $pass, $s_date, $e_date, $game)
    {
        switch ($game) {
            case '1':
                $gameid=60;//真人
                break;
            case '2':
                $gameid=61;//體育
                break;
            case '3':
                $gameid=64;//新體育
                break;
            case '4':
                $gameid=63;//彩票
                break;  
            case '5':
                $gameid=65;//捕魚
                break;
            case '6':
                $gameid=66;//捕魚大師
                break;
            case '7':
                $gameid=255;//小費
                break;    
            case '8':
                $gameid=62;//電子
                break;
            default:
                # code...
                break;
        }
        $query = "SELECT SUM(payout) AS payout FROM p_new_rpt WHERE categoryid=$gameid AND matchdate BETWEEN '$s_date' AND '$e_date'";
        $data = $this->dbconnect($host, $db, $user, $pass, $query);
        return $data[0]['payout'];
    }

   
    public function delete($host, $db, $user, $pass, $s_date, $e_date)
    {
        $query = "DELETE FROM `p_betslips_sb` WHERE trans_id IN(SELECT * FROM (SELECT trans_id FROM `p_betslips_sb` WHERE orderdate BETWEEN '$s_date' AND '$e_date' GROUP BY trans_id HAVING COUNT(*)>1) as p)";

        $dbms = 'mysql';
        $dbName = $db . '_intergration';
        $dsn = "$dbms:host=$host;dbname=$dbName";

        if ($host == '10.10.1.96') {
            $dbName = 'integration';
            $dsn = "$dbms:host=$host;dbname=$dbName";
        }

        try {
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->exec("SET CHARACTER SET utf8");
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error!: " . $e->getMessage() . "<br/>");
        }
        $state = $dbh->query($query);
        echo $db . '删除完毕' . PHP_EOL;

        // return $data[0]['payout'];
    }
}

if ($argv[1] == 'prefix') {
    #抓取站台列表

    $x = new sqldata();
    $prefixlist = $x->prefix();
    foreach ($prefixlist as $v) {
        $result[] = $v['Prefix'];
    }
    print_r(json_encode($result));

} else if($argv[1] == 'prefix_bb'){

    $x = new sqldata();
    $prefixlist = $x->prefix_bb();
    foreach ($prefixlist as $v) {
        $result[$v['Prefix']] = $v['bbagent'];
    }
    print_r(json_encode($result));
}
else{
    if ($argv[3] == 'delete') {
        fwrite(STDOUT, "删除沙巴重复单用的!!!!确定执行?(y/n)");
        $check = trim(fgets(STDIN));
        $check != 'y' ? exit() : false;
    }
    $s_date = $argv[1];
    $e_date = $argv[2];
    $name = $argv[3];
    $game = $argv[4];
    $plat = $argv[5];
    $accountcode = $argv[6];
    $x = new sqldata();
    $x->index($s_date, $e_date, $name, $game, $plat, $accountcode);
}
