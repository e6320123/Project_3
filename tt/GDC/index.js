const SUB=require('./GDC');
const colors = require('colors');
//const share = require('../share_modules/sharing_dennis');
//main_sub_data第三方資料function
//sub_log 對方資料
//bos_log 我方資料
//err_log 資料err_log
//sql 我方資料sql 資料格式SELECT sum(activebet) as activebet,SUM(`payout`) as payout FROM `p_new_rpt`"
//main_sub_data資料格式{[activebet: "729712.9500",payout: "-2029.6000",prefix: "663"],[],[]....}
// main(main_sub_data,sub_log,bos_log,err_log,sql);

const url='https://gdc.agingames.com';
//新線
const user_data={
    da6:{user:"DA6.bet",pass:"uKlQiUrXJx3",webs:['BET', 'MMY', 'YH6', 'T88', 'JJT','V99','BCN','TK8','B07','TEA','188','DFA','GBT', 'A11','RB8']},
    da7:{user:"DA7.yung",pass:"xfqtk7Oner",webs:['LIS', 'XXP']},
    da8:{user:"DA8.jinsha",pass:"yxF8OEVMx5",webs:['VIP', 'YHH', 'WNS', 'JSS', 'B38', 'WEI', 'VVN', 'B65', 'X88','999','DEV']},
    db0:{user:"DB0.yinhe",pass:"4GPzR0HS71",webs:['B70', 'B66', 'B33', 'B88', 'XH8', 'X9J','222','TBB', 'SVIP', 'A88']},
    db1:{user:"DB1.xpj",pass:"uFx9mneEIC",webs:['N88', 'BB3', 'ZWNS', 'J95', 'B96', 'J70', 'Z88', 'VNC', 'V66','TY9', '3369','T56']},
    db2:{user:"DB2.dafa",pass:"B15wQB188X",webs:['JSY', 'VER', 'Y09', '663', 'P58', 'TY8', 'P88','BB6','B25','BEA', 'MVP', 'HG9', 'BS8', 'NO6', 'BT5']},
    db3:{user:"DB3.taiyang",pass:"eihqqTu4uI",webs:['B73', 'PJ8', 'SCC', 'RX9', 'YYL', 'J32', 'T63', 'JSC','911','111', 'GPK', 'MM8','9J3','088','T67']},
    db4:{user:"DB4.boshi",pass:"nLZt2h7c5E",webs:['B68', 'BES', 'B80', 'VVV', 'VEN', 'B77', 'B98', 'XG8','3368', '345', 'B18', 'ZZZ']},
    db5:{user:"DB5.huanggu",pass:"LmG3BmgjYh",webs:['HG8', 'TGO', 'XXC', 'YLG', 'DFC', 'W97', 'Y68', 'TTB', 'E88', 'AXPJ','V88', 'K99', '889']},
    db6:{user:"DB6.xingji",pass:"kZgswGTE0v",webs:['QWE', 'DSH', 'JIN', 'BTT', 'B32', 'BTE', 'FLL', 'BT9', 'JSJ', 'F88', '668', 'J89','KK8']},
    db7:{user:"DB7.bolong",pass:"lO9rRYqWTa",webs:['VBM', 'KTS', 'PP1', 'T38', '678', 'XTD', 'CCC', 'VAG','TTT','555', 'D5G', 'H28','669']},
    da9:{user:"DA9.weinisi",pass:"1CvLUCIFvl",webs:['XPS', 'JS2', 'CAN', 'XP2', '66S', 'XAB', 'MAX','333','BGE','ABC','3668','YH5', 'B28', 'A55', 'B83']},
    dk6:{user:"DK6.BET365",pass:"gjoMwSMnhO",webs:['B131', 'TBG', 'YPW', 'J27', 'B58', '605', 'T30', 'BBE','NO1','BOB', 'PJW', 'T66','GZC','8YH']},
    ex3:{user:"EX3.jinsha",pass:"neW@Up!Iq#",webs:['JJJ', 'B89', 'B95', '168','SAS','888','777','KKK','B86','007','789','wls', 'NO8', 'NO9', 'B91', '123','T10']},
  };
var start_d = process.argv[2];
var end_d = process.argv[3];
var type = process.argv[4];

const date={
  start:start_d,
  end:end_d,
}
// const date={
//   start:'2019-09-03',
//   end:'2019-09-03',
// }

var webs={
    AGIN_live:   {categoryid:'17', activebet_ntd:'12',  payout_ntd:'13', url:'/jsp/secured/queryOrderInfoAGIN.htm?pid=',
                script:()=>{
                        document.querySelector('#Status-inputEl').value=1;
                }},

    // AGIN_HUNTER: {categoryid:'49', activebet_ntd:'12',  payout_ntd:'14', url:'/jsp/secured/queryOrderInfoALL.htm?pid=',
    //               jackpot:true, script: () => {
    //                 document.getElementById('checkall1-inputEl').click();
    //                 document.getElementById('checkboxforHUNTER-inputEl').click();}},

    AGIN_SPORT:  {categoryid:'47', activebet_ntd:'11',  payout_ntd:'12', url:'/jsp/secured/queryOrderInfoSBTA.htm?pid=',
                script:() => {document.querySelector('#TimeSelect-inputEl').value='settleTime';}},

    AGIN_EGAME:  {categoryid:'50', activebet_ntd:'11',  payout_ntd:'12', url:'/jsp/secured/queryOrderInfoXIN.htm?pid=',
                script:() => {document.getElementById('Brand-inputEl').value='AG';}},

    AGIN_XIN:    {categoryid:'59', activebet_ntd:'11',  payout_ntd:'12', url:'/jsp/secured/queryOrderInfoXIN.htm?pid=',
                script:() => {document.getElementById('Brand-inputEl').value='XIN';}},

    AGIN_YOPLAY: {categoryid:'48', activebet_ntd:'10',  payout_ntd:'11', url:'/jsp/secured/queryOrderInfoYOPLAY.htm?pid=',},
};
SUB.main_sub_data(url,webs,user_data,date);
//share.main(SUB.main_sub_data(url,webs,user_data,date),webs,date,false);
