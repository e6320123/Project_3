<?php

class SqlTool {
    public $preArr      = [ 'hg8','tgo','xxc','qwe','dsh','btt','663','678','t38','bte','y68' ]; // 如果没输入站台 为全站SQL
    public $pauseFlag   = true;     // 开启此功能的话 会一站一站确认是否执行
    public $sqls    = [
        "UPDATE PlatformProduct SET status = 'Active' WHERE id in('174');",
        "SELECT `name`, `status` FROM PlatformProduct WHERE id IN ('174');",
    ];

    public $sDatas      = [];

    public function __construct() {
        $this->getSites();
    }

    function getSites() {
        try{
            $db_pdo = new \PDO('mysql:host=10.10.1.96;dbname=integration;charset=utf8', 'int', 'integration111');
            $sql = "SELECT Prefix, dbip, dbprefix, dbuser, dbpass FROM `Company` WHERE `status` != 'Closed'";
            $statement = $db_pdo->query($sql);

            while($row = $statement->fetch(PDO::FETCH_OBJ)) {
                $this->sDatas[$row->Prefix] = [
                    'dbPrefix'  => $row->dbprefix,
                    'dbIp'      => $row->dbip,
                    'dbUser'    => $row->dbuser,
                    'dbPass'    => $row->dbpass,
                ];
            }
            $db_pdo = null;
        }catch(PDOException $e) {
            print_r($e->getMessage());
            exit;
        }
    }

    private function pdoNew($prefix, $dbprefix, $dbip, $dbuser, $dbpass) {
        try{
            printf("========== %s: %s ==========\n", $prefix, $dbip);
            $pdoPrepare = sprintf("mysql:host=%s;dbname=%s;charset=utf8", $dbip, $dbprefix.'_intergration');

            if($this->pauseFlag) {
                $errorCounter= 0;
                while($errorCounter < 3) {
                    printf("> 继续执行 按y(Y) ,跳过执行 按c(C) ,关闭程式 按q(Q): ");
                    $input =strtoupper(trim(fgets(STDIN)));
                    if($input === 'Y') {
                        return new \PDO($pdoPrepare, $dbuser, $dbpass);
                    }elseif($input === 'C') {
                        return null;
                    }elseif($input === 'Q') {
                        exit('离开程式...');
                    }else {
                        $errorCounter ++;
                        print_r('错误次数: '. $errorCounter . PHP_EOL);
                    }
                }
                exit("\033[47;31m耖 我不会说你手残...\033[0m\n");
            }else {
                return new \PDO($pdoPrepare, $dbuser, $dbpass);
            }

        }catch(PDOException $e) {
            print_r($e->getMessage());
            exit;
        }
    }

    public function run() {
        if(empty($this->preArr)) {
            $this->preArr = array_keys($this->sDatas);
        }
        
        foreach($this->preArr as $prefix) { //站台开始跑
            $pdoObj = $this->pdoNew($prefix, $this->sDatas[$prefix]['dbPrefix'], $this->sDatas[$prefix]['dbIp'], $this->sDatas[$prefix]['dbUser'], $this->sDatas[$prefix]['dbPass'] );
            if(is_null($pdoObj)) 
                continue;
            
            foreach($this->sqls as $sql) {
                printf("\033[0;32m%s \033[0m\n", $sql);
                try {
                    if(!preg_match('/where/Ui', $sql)) {
                        exit("\033[47;31m不存在where 不给执行...\033[0m\n");
                    }

                    $sql = trim($sql);
                    $sth = $pdoObj->prepare($sql);
                    $sth->execute();

                    $sqlFirst = explode(' ', strtoupper($sql))[0];
                    if ($sqlFirst === 'SELECT') {
                        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
                            print_r($row);
                        }
                    } elseif($sqlFirst === 'UPDATE' || $sqlFirst === 'DELETE') {
                        $count = $sth->rowCount();
                        printf("\033[1;31m引响行数: %d \033[0m\n", $count);
                    }else {
                        exit("\033[47;31mNo match method...\033[0m\n");
                    }
                }catch(\PDOException $e) {
                    print_r($e->getMessage());
                    exit;
                }
            }
            $pdoObj = null;
        }
    }
}



$sqltool = new SqlTool();

$sqltool->run();
