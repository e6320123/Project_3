const fs = require('fs');
const puppeteer = require('puppeteer');
//const share = require('../share_modules/sharing_dennis');
delay=(time) =>{
   return new Promise(function(resolve) { 
       setTimeout(resolve, time)
   });
}
exports.main_sub_data = async (url,SUB_webs,user_data,date,old='') => {
  //創建資料夾
  await fs.exists('cookies', function(exists) {
    if(!exists){
      fs.mkdir('cookies', function (err) {
      console.log("創建成功cookies");
    });
    }
  });
  

  var arr=[];
  //抓取資料 已經有暫存就跳過
  for(var key in user_data){
     arr.push(this.curl(url,key,user_data[key],SUB_webs,date));
  }
  //同時必行
  let promiseResult = await Promise.all(arr);
  //console.log(promiseResult);
  
  await console.log('處理暫存檔案');
  //處理暫存檔案
  var result={};
  for(var log in SUB_webs){

    result[log]=[];
    for(var value of promiseResult){
      result[log]=result[log].concat(value[log]);
    }

  }
  //console.log(result);
  
  await delay(100);

  return result;
};

exports.curl = async (url,key,value,SUB_webs,date) => {
    var result={};//資料
    var start=date.start+" 00:00:00";
    var end=date.end+" 23:59:59";
    var subfolder='tmp_'+date.start+'_'+date.end; //暫存子資料夾
    var tmp_log_line=subfolder+'/'+key+'_tmp.log'; //暫存log
    var tmp_obj; //暫存obj
    var cookie_log='cookies/'+key+'cookie.log';
    var SUB_cookie;

    console.log(key);

    //讀取cookie
    await fs.readFile(cookie_log, function (err, data) {
      if(!err){
        var tmp=data.toString();
        SUB_cookie=JSON.parse(tmp);
      }
    });
    await delay(100);

    //是否存在tmp資料表
    await fs.exists(subfolder, function(exists) {
      if(!exists){
        fs.mkdir(subfolder, function (err) {
        console.log("創建成功");
      });
      }
    });
    //讀取檔案
    await fs.readFile(tmp_log_line, function (err, data) { if(!err) tmp_obj=JSON.parse(data.toString())})
    await delay(100);
    if(tmp_obj){
      await console.log(tmp_log_line+"已經存在");
      return tmp_obj;
    }
    var browser,page;
    if(SUB_cookie){
      browser = await puppeteer.launch({headless: false,args: ['--start-maximized']});
      page = await browser.newPage();
      await page.setViewport({width: 1920,height: 1080});
      await page.setExtraHTTPHeaders(SUB_cookie);
      var home_url=url+'/jsp/secured/home.htm';
      await page.goto(home_url);
      if(await page.url().match('login.htm')){
        await console.log("被登出");
        var unlink=false;
        await fs.unlink(cookie_log, (err) => {
          if (!err) { 
            unlink=true; 
            console.log('刪除cookie:'+cookie_log);
          };
        });
        await delay(100);
        if(unlink){
          console.log('重新登入');
          await browser.close();
          return this.curl(url,key,value,SUB_webs,date);
        }
      }
    }else{
      browser = await puppeteer.launch({headless: false});
      page = await browser.newPage();
      var login_url=url+'/jsp/login.htm';
      await page.goto(login_url);
      await console.log('等待中');
      await page.waitFor(() => document.querySelector('form'));
      await console.log('登入');
      await page.type('input[name="j_username"]', value.user);
      await page.type('input[name="j_password"]', value.pass);
      cmd_str= await key+'輸入驗證碼';
      await console.log(cmd_str);
      await page.waitFor(() => document.querySelector('#main-header'), { timeout: 120000 });
      SUB_cookie={};
      tmp_cookie=await page.cookies();
      tmp_arr=[];
      for(var val_cok of tmp_cookie){
        tmp_arr.push(val_cok['name']+'='+val_cok['value']);
      }
      SUB_cookie.Cookie=await tmp_arr.join(';');
      await fs.writeFileSync(cookie_log,JSON.stringify(SUB_cookie));
    }
      //await page.screenshot({path: 'img/'+key+'_signup.png',fullPage:true});


    for(var SUB_web in SUB_webs){
      result[SUB_web]=[];
      var SUB_data=SUB_webs[SUB_web];
      await console.log('跳轉'+SUB_web+'頁面');
      await page.goto(url+SUB_data.url+key.toUpperCase()+'&sstr=');
      await page.waitFor(() => document.querySelector('[id*="queryInfo"]'), { timeout: 60000 });

      await console.log('篩選資料');
      await page.evaluate((start,end)=>{
        document.getElementById('FromTime-inputEl').value=start;
        document.getElementById('ToTime-inputEl').value=end;
        document.getElementById('likeQueryLoginName-inputEl').click();
        document.getElementById('PageNum-inputEl').value=1;
      },start,end)
      if(SUB_data.script) await page.evaluate(SUB_data.script);
      //抓取個站資料
      for(var web of value.webs){
        await console.log('查詢'+web);
        web=web.toLowerCase();
        await page.evaluate((web)=>{
          document.getElementById('MemberId-inputEl').value=web;
          document.getElementById("searchBtn-btnIconEl").click();
        },web)
        await page.waitFor(() => document.querySelector('#gv-body > tr:last-child'));
        //await page.waitFor(200000);
        //await console.log('查詢完畢'+web);
        // if(SUB_web =='AGIN_HUNTER'){
        //   result[SUB_web].push(await page.evaluate((prefix,activebet_ntd,payout_ntd,jackpot_ntd) => {
        //     let element = document.querySelector('#gv-body > tr:last-child');

        //     var activebet = parseFloat(element.querySelector('td:nth-child('+activebet_ntd+')').innerText.trim().replace(/,/g,""));
        //     var payout    = parseFloat(element.querySelector('td:nth-child('+payout_ntd+')').innerText.trim().replace(/,/g,""));
        //     var jackpot = parseFloat(element.querySelector('td:nth-child('+jackpot_ntd+')').innerText.trim().replace(/,/g,""));
        //     payout = payout + jackpot;
        //     document.querySelectorAll('#gv-body tr').forEach(item=>item.remove());
        //     return {prefix, activebet,payout};
        //   },web,SUB_data.activebet_ntd,SUB_data.payout_ntd,SUB_data.jackpot_ntd))
        // }
        // else{
        //   result[SUB_web].push(await page.evaluate((prefix,activebet_ntd,payout_ntd) => {
        //     let element = document.querySelector('#gv-body > tr:last-child');

        //     var activebet = parseFloat(element.querySelector('td:nth-child('+activebet_ntd+')').innerText.trim().replace(/,/g,""));
        //     var payout    = parseFloat(element.querySelector('td:nth-child('+payout_ntd+')').innerText.trim().replace(/,/g,""));
        //     document.querySelectorAll('#gv-body tr').forEach(item=>item.remove());
        //     return {prefix, activebet,payout};
        //   },web,SUB_data.activebet_ntd,SUB_data.payout_ntd))
        // }
        result[SUB_web].push(await page.evaluate((prefix,activebet_ntd,payout_ntd) => {
          let element = document.querySelector('#gv-body > tr:last-child');

          var activebet = parseFloat(element.querySelector('td:nth-child('+activebet_ntd+')').innerText.trim().replace(/,/g,""));
          var payout    = parseFloat(element.querySelector('td:nth-child('+payout_ntd+')').innerText.trim().replace(/,/g,""));
          document.querySelectorAll('#gv-body tr').forEach(item=>item.remove());
          return {prefix, activebet,payout};
        },web,SUB_data.activebet_ntd,SUB_data.payout_ntd))
       // await console.log('查詢完畢'+web,result[SUB_web]);
        //await page.waitFor(2000);

      }
      console.log(result[SUB_web]);
    }
    //儲存暫存檔
    await console.log(tmp_log_line+"儲存暫存檔");
    await fs.writeFileSync(tmp_log_line, JSON.stringify(result));

    await browser.close();

    return result;
}

