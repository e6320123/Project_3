const SUB=require('./AB');
const colors = require('colors');
//const share = require('../share_modules/sharing_dennis');
//main_sub_data第三方資料function
//sub_log 對方資料
//bos_log 我方資料
//err_log 資料err_log
//sql 我方資料sql 資料格式SELECT sum(activebet) as activebet,SUM(`payout`) as payout FROM `p_new_rpt`"
//main_sub_data資料格式{[activebet: "729712.9500",payout: "-2029.6000",prefix: "663"],[],[]....}
// main(main_sub_data,sub_log,bos_log,err_log,sql);

const url='https://ams.allbetgaming.net';
//新線
const user_data={
    bos:{user:"bgcs2",pass:"Aa123456@",agentid:1165,webs:[]},
    xxp:{user:"bgecsxpj",pass:"1G2rq0e#",agentid:1166,webs:[]},

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
    AB_live:   {categoryid:'17', activebet_ntd:'12',  payout_ntd:'13', url:'/#/report/profit',
                script:()=>{
                        document.querySelector('#Status-inputEl').value=1;
                }},
};
SUB.main_sub_data(url,webs,user_data,date);
//share.main(SUB.main_sub_data(url,webs,user_data,date),webs,date,false);
