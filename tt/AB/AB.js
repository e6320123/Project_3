const fs = require('fs');
const puppeteer = require('puppeteer');
const { exit } = require('process');
//const share = require('../share_modules/sharing_dennis');
delay=(time) =>{
   return new Promise(function(resolve) { 
       setTimeout(resolve, time)
   });
}

Date.prototype.yyyymmdd = function() {
  var mm = this.getMonth() + 1; // getMonth() is zero-based
  var dd = this.getDate();

  return [this.getFullYear(),
          (mm>9 ? '' : '0') + mm,
          (dd>9 ? '' : '0') + dd
         ].join('-');
};

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
    var start=date.start;
    var endt = new Date(date.end);
    endt.setDate(endt.getDate()+1);
    var end = endt.yyyymmdd();
    
    var subfolder='ab_tmp_'+date.start+'_'+date.end; //暫存子資料夾
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

    //正式開抓
    var browser,page;
    var urlprofit = "https://ams.allbetgaming.net/#/profitReport";
    // if(SUB_cookie)

      browser = await puppeteer.launch({
        defaultViewport: null,
        headless: false
      });
      page = await browser.newPage();
      var login_url=url+'/#/login.htm';
      await page.goto(login_url);
      await console.log('等待中');
      await page.waitFor(() => document.querySelector('form'));
      await console.log('登入');
      await page.type('input[type="text"]', value.user);
      await page.type('input[type="password"]', value.pass);
      cmd_str= await key+'輸入驗證碼';
      await console.log(cmd_str);
      await page.waitFor(() => document.querySelector('#header'), { timeout: 120000 });
      SUB_cookie={};
      tmp_cookie=await page.cookies();
      tmp_arr=[];
      for(var val_cok of tmp_cookie){
        tmp_arr.push(val_cok['name']+'='+val_cok['value']);
      }

    for(var SUB_web in SUB_webs){
      var SUB_data=SUB_webs[SUB_web];
      result[SUB_web]=[];
      // await page.waitForNavigation({waitUntil: 'domcontentloaded'});
      //await page.waitFor('#scroll-inner > div.os-padding > div > div > div > div > ul > li.el-menu-item.is-active.menu-item-active');
      await page.waitFor(2000);
      await page.goto(urlprofit);
      await page.waitFor(2000);
      
      await page.click("#scroll-inner > div > div > div.day-range > div > div.picker.datetimerange.datetime > div");
      await page.waitFor(2000);
      await page.evaluate(()=>document.querySelector('body > div.el-picker-panel.el-date-range-picker.el-popper.has-time.ams-timeslot-popper > div.el-picker-panel__body-wrapper > div > div.el-date-range-picker__time-header > span:nth-child(1) > span:nth-child(1) > div > input').value = "");
      await page.waitFor(2000);
      await page.type('body > div.el-picker-panel.el-date-range-picker.el-popper.has-time.ams-timeslot-popper > div.el-picker-panel__body-wrapper > div > div.el-date-range-picker__time-header > span:nth-child(1) > span:nth-child(1) > div > input',start);
      await page.waitFor(2000);
      
      //await page.click("body > div:nth-child(7) > div.el-picker-panel__footer > button.el-button.el-picker-panel__link-btn.el-button--default.el-button--mini.is-plain > span");          
      // await page.type('body > div.el-picker-panel.el-date-picker.el-popper.has-time > div.el-picker-panel__body-wrapper > div > div.el-date-picker__time-header > span:nth-child(1) > div > input',start);
      // await page.waitFor(2000);
      //await page.waitFor(2000);
      
      
      //await page.click('#scroll-inner > div > div > div.day-range > div > div.picker.datetime > div:nth-child(3) > input');
      //await page.waitFor(2000);
      await page.evaluate(()=>document.querySelector('body > div.el-picker-panel.el-date-range-picker.el-popper.has-time.ams-timeslot-popper > div.el-picker-panel__body-wrapper > div > div.el-date-range-picker__time-header > span.el-date-range-picker__editors-wrap.is-right > span:nth-child(1) > div > input').value = "");
      await page.waitFor(2000);
      await page.type('body > div.el-picker-panel.el-date-range-picker.el-popper.has-time.ams-timeslot-popper > div.el-picker-panel__body-wrapper > div > div.el-date-range-picker__time-header > span.el-date-range-picker__editors-wrap.is-right > span:nth-child(1) > div > input',end);
      await page.waitFor(2000);
      await page.click("body > div.el-picker-panel.el-date-range-picker.el-popper.has-time.ams-timeslot-popper > div.el-picker-panel__footer > button.el-button.el-picker-panel__link-btn.el-button--default.el-button--mini.is-plain > span");
      await page.waitFor(2000);
      // await page.type('body > div.el-picker-panel.el-date-picker.el-popper.has-time > div.el-picker-panel__body-wrapper > div > div.el-date-picker__time-header > span:nth-child(1) > div > input',end);
      // await page.waitFor(2000);
   
      await page.click("#scroll-inner > div > div > div.day-range > div > button:nth-child(2) > span");


    
      
      await page.waitFor('#profit-list > div > div.agent-group.group-ui.none-group > div > div > div > div.list-sub-item.d-flex.align-items-center');
      await page.waitFor(3000);

      // 當結果數出現 顯示更多 時 拿掉註解
      // if(await page.waitForSelector('#profit-list > div > div:nth-child(101) > div > span',{timoeout: 5000}))
      // {
      //   await page.click('#profit-list > div > div:nth-child(101) > div > span');
      //   await page.waitFor(3000);
      // }


      result[SUB_web] = await page.evaluate(() => {
        var hasClass = function(elem, className) {
          var reg = new RegExp('(^|\\s+)' + className + '($|\\s+)');
          return reg.test(elem.className);
        };
        
        let data = [];
        let games = document.querySelectorAll('#profit-list > div > div > div');
        

        var prefix_match = function(prefix){
          if(prefix == 'bgtbgy5') prefix = 'txx';
          prefix = prefix.replace("bg", "");
          prefix = prefix.replace("y5", "");
          prefix = prefix.replace("b_", "");
          prefix = prefix.replace("代理报表", "");
          prefix = prefix.replace("jsh", "jss");
          prefix = prefix.replace("bet88", "t88");
          prefix = prefix.replace("bet36", "bet");
          prefix = prefix.replace("bcna", "cna");
          prefix = prefix.replace("bzwns", "zwns");
          prefix = prefix.replace("b1xxp", "xxp");
          prefix = prefix.replace("blg", "blgj");
          prefix = prefix.replace("bb131", "b131");
          prefix = prefix.replace("b3368", "3368");
          prefix = prefix.replace("b3668", "3668");
          prefix = prefix.replace("baxpj", "axpj");
          prefix = prefix.replace("b1xxp", "xxp");
          prefix = prefix.replace("bsvip", "svip");
          prefix = prefix.replace("b3369", "3369");
          if(prefix == 'txx') prefix = 'tbg';
          return prefix;
        }

        for(var game of games){
          if(hasClass(game,'groups'))
          {
            let prefix = game.querySelector("div > div > div > div.name.list-sub-item-col.text-center.justify-content-center > span.text-link.text-underline").innerText.trim(); 
            prefix = prefix_match(prefix);      
            let payout =game.querySelector("div > div > div > div.agent-list-detail > div > div.list-sub-item.d-flex.align-items-center.total > div.right.d-flex > div:nth-child(2) > span > span").innerText.trim();
    
            data.push({prefix, payout});
          }
        }
        return data;
     });

    }
    //await page.waitFor(()=>document.querySelector('.abg7777'));

    //儲存暫存檔
    await console.log(tmp_log_line+"儲存暫存檔");
    await fs.writeFileSync(tmp_log_line, JSON.stringify(result));

    //await page.waitFor('#77777777');
    await browser.close();

    return result;
}


