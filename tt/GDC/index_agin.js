const SUB=require('./AGIN');
const colors = require('colors');
//const share = require('../share_modules/sharing_dennis');
//main_sub_data�������Y��function
//sub_log �����Y��
//bos_log �ҷ��Y��
//err_log �Y��err_log
//sql �ҷ��Y��sql �Y�ϸ�ʽSELECT sum(activebet) as activebet,SUM(`payout`) as payout FROM `p_new_rpt`"
//main_sub_data�Y�ϸ�ʽ{[activebet: "729712.9500",payout: "-2029.6000",prefix: "663"],[],[]....}
// main(main_sub_data,sub_log,bos_log,err_log,sql);


const url='https://data.agingames.com';
//�¾�
const user_data={
    da6:{user:"gbosda6cs",pass:"4LWejcWL",webs:['BET', 'MMY', 'YH6', 'T88', 'JJT','V99','BCN','TK8','B07','TEA','188','DFA','GBT', 'A11','RB8']},
    da7:{user:"gbosda7cs",pass:"tyKttpB51",webs:['LIS', 'XXP']},
    da8:{user:"gbosda8cs",pass:"ZmS2ZrvU",webs:['VIP', 'YHH', 'WNS', 'JSS', 'B38', 'WEI', 'VVN', 'B65', 'X88','999','DEV']},
    db0:{user:"gbosdb0cs",pass:"2e9PFTQy",webs:['B70', 'B66', 'B33', 'B88', 'XH8', 'X9J','222','TBB', 'SVIP', 'A88']},
    db1:{user:"gbosdb1cs",pass:"WN9eMKX2",webs:['N88', 'BB3', 'ZWNS', 'J95', 'B96', 'J70', 'Z88', 'VNC', 'V66','TY9', '3369','T56']},
    db2:{user:"gbosdb2cs",pass:"MMq3r8Mx",webs:['JSY', 'VER', 'Y09', '663', 'P58', 'TY8', 'P88','BB6','B25','BEA', 'MVP', 'HG9', 'BS8', 'NO6', 'BT5']},
    db3:{user:"gbosdb3cs",pass:"B75d9kWV1",webs:['B73', 'PJ8', 'SCC', 'RX9', 'YYL', 'J32', 'T63', 'JSC','911','111', 'GPK', 'MM8','9J3','088','T67']},
    db4:{user:"gbosdb4cs",pass:"JHRhrtDq",webs:['B68', 'BES', 'B80', 'VVV', 'VEN', 'B77', 'B98', 'XG8','3368', '345', 'B18', 'ZZZ']},
    db5:{user:"gbosdb5cs",pass:"9QasR2Bs",webs:['HG8', 'TGO', 'XXC', 'YLG', 'DFC', 'W97', 'Y68', 'TTB', 'E88', 'AXPJ','V88', 'K99', '889']},
    db6:{user:"gbosdb6cs",pass:"Kgmxraz56",webs:['QWE', 'DSH', 'JIN', 'BTT', 'B32', 'BTE', 'FLL', 'BT9', 'JSJ', 'F88', '668','j89','kk8']},
    db7:{user:"gbosdb7cs",pass:"VPB32rbN",webs:['VBM', 'KTS', 'PP1', 'T38', '678', 'XTD', 'CCC', 'VAG','TTT','555', 'D5G', 'H28','669']},
    da9:{user:"gbosda9cs",pass:"kD7Ax6NX",webs:['XPS', 'JS2', 'CAN', 'XP2', '66S', 'XAB', 'MAX','333','BGE','ABC','3668','YH5', 'B28', 'A55', 'B83']},
    dk6:{user:"gbosdk6cs",pass:"bos15688",webs:['B131', 'TBG', 'YPW', 'J27', 'B58', '605', 'T30', 'BBE','NO1','BOB', 'PJW', 'T66','GZC','8yh']},
    ex3:{user:"gbosex3cs",pass:"i0dlcH715de",webs:['JJJ', 'B89', 'B95', '168','SAS','888','777','KKK','B86','007','789','wls', 'NO8', 'NO9', 'B91', '123','T10']},
  };
var start_d = process.argv[2];

var end_d = process.argv[3];
const date={
  start:start_d,
  end:end_d,
}


// const date={
//   start:'2019-09-16',
//   end:'2019-09-16',
// }

if(process.argv[4] == '1'){
    //���~
    var webs={
        AGIN_HUNTER:   {url:'/memberreport',},
    };
}
else if (process.argv[4] == '2'){
    //��ِ���
    var webs={
        AGIN_comp:   {url:'/competitionsystem',},
    };
}
else if (process.argv[4] == '3'){
    var webs={
        //�t���o�
        AGIN_redpocket:   {url:'/redpocket',},
    };
}
else if(process.argv[4] == '4'){
    var webs={
        //���~���
        AGIN_hunterevt:   {url:'/hunterevent',},
    };
}
else if(process.argv[4] == '5'){
    var webs={
        //Ԫ����Ʊ
        AGIN_lanternLottery:   {url:'/lanternLottery',},
    };
}

SUB.main_sub_data(url,webs,user_data,date);

