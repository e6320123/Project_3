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
  await fs.exists('agin_cookies', function(exists) {
    if(!exists){
      fs.mkdir('agin_cookies', function (err) {
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
    var subfolder='agin_tmp_'+date.start+'_'+date.end; //暫存子資料夾
    var tmp_log_line=subfolder+'/'+key+'_tmp.log'; //暫存log
    var tmp_obj; //暫存obj
    var cookie_log='agin_cookies/'+key+'cookie.log';
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
      browser = await puppeteer.launch({headless: false,args: ['--start-maximized','--disable-gpu','--no-first-run','--no-sandbox','--no-zygote']});
      page = await browser.newPage();
      await page.setViewport({width: 1920,height: 1080});
      await page.setExtraHTTPHeaders(SUB_cookie);
      var home_url=url+'/promotiongame';
      await page.goto(home_url);
      if(await page.url().match(url)){
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
    }
    else{
      browser = await puppeteer.launch({headless: false, args: ['--start-maximized','--disable-gpu','--no-first-run','--no-sandbox','--no-zygote']});
      page = await browser.newPage();
      await page.setViewport({width: 1920,height: 1080});
      var login_url=url;
      await page.goto(login_url);
      await console.log('等待中');
      await page.waitFor(() => document.querySelector('form'));
      await console.log('登入');
      await page.type('input[name="account"]', value.user);
      await page.type('input[name="password"]', value.pass);
      await page.click('#loginButton');
      await page.waitFor(() => document.querySelector('.main-temp'), { timeout: 120000 });
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

      var st_dt = new Date(start);
      var st_y = new String(st_dt.getFullYear());
      var st_m = new String(st_dt.getMonth());
      var en_dt = new Date(end);
      var en_y = new String(en_dt.getFullYear());
      var en_m = new String(en_dt.getMonth());


      await console.log('跳轉'+SUB_web+'頁面');
      await page.goto(url+SUB_data.url);
      await page.waitFor(() => document.querySelector('#tab2'));

      if(SUB_web == 'AGIN_HUNTER'){
        await page.goto(url+SUB_data.url+'#!#tab3');
        await page.evaluate(() => {
          document.querySelector("#aginBackend > ul > li:nth-child(1)").setAttribute('class','');
          document.querySelector("#aginBackend > ul > li:nth-child(3)").setAttribute('class','active');
          document.getElementById('index').setAttribute('class','tab-pane ng-scope');
          document.getElementById('tab3').setAttribute('class','tab-pane ng-scope active');
        })
        //選擇開始日期
        await page.click('#tab3 > div > div > div > div > div.search-area > div > form > div.control-group.input-daterange.datetimepicker > div >input:nth-child(1)');
        await ChoseDate(1,st_dt, st_y, st_m);

        //選擇結束日期
        await page.click('#tab3 > div > div > div > div > div.search-area > div > form > div.control-group.input-daterange.datetimepicker > div > input:last-child');
        await ChoseDate(1,end, en_y, en_m);
        
        await page.click('#baseFormHunterpark_fz > span');
        //抓各站資料
        for(var web of value.webs){
          await page.evaluate(() => {
            document.querySelector('#tab3 > div > div > div > div > div.search-area > div > form > div:nth-child(2) > div > input').value="";
          })
          await page.waitFor(2000);
          //輸入各站
          await page.click('#tab3 > div > div > div > div > div.search-area > div > form > div:nth-child(2) > div > input');
          await page.type('#tab3 > div > div > div > div > div.search-area > div > form > div:nth-child(2) > div > input', web.toLowerCase());
          await page.waitFor(2000);

          //查詢
          await page.click('#tab3 > div > div > div > div > div.search-area > div > form > div:nth-child(12) > div > button.sui-btn.btn-primary');
          await page.waitFor(5000);
          await page.waitFor(() => document.querySelector('#memberreportHunterparkTB > tbody > tr'));
          result[SUB_web].push(await page.evaluate((prefix) => {
            var payout = 0;
            var ifdata = document.querySelector('#memberreportHunterparkTB > tbody > tr > td').innerText;
            if(ifdata != '(^o^),無記錄!'){
              let element = document.querySelector('#memberreportHunterparkTB > tbody > tr:last-child');
              var m_payout = parseFloat(element.querySelector('td:nth-child(6) > div > span').innerText.replace(/,/g,""));
              var japk = parseFloat(element.querySelector('td:nth-child(7) > div > span').innerText.replace(/,/g,""));
              var j_wining = parseFloat(element.querySelector('td:nth-child(8) > div > span').innerText.replace(/,/g,""));
              // var collect_win =parseFloat(element.querySelector('td:nth-child(10) > div > span').innerText.replace(/,/g,""));
              // var one_win = parseFloat(element.querySelector('td:nth-child(11) > div > span').innerText.replace(/,/g,""));
              payout = m_payout - japk + j_wining ;
            }
            

            return {prefix, payout};
          },web))
        }
      
      }

      //紅包紀錄
      if(SUB_web == 'AGIN_redpocket'){
        
        await page.goto(url+SUB_data.url+'#!#tab2');
        await page.evaluate(() => {
          document.querySelector("#contentBody > div:nth-child(7) > div.sui-layout.content-group > div > ul >li:nth-child(1)").setAttribute('class','');
          document.querySelector("#contentBody > div:nth-child(7) > div.sui-layout.content-group > div > ul >li:nth-child(2)").setAttribute('class','active');
          document.getElementById('tab1').setAttribute('class','tab-pane ng-scope');
          document.getElementById('tab2').setAttribute('class','tab-pane ng-scope active');
        })
        
        await page.click('#tab2>div:nth-child(2)>div>form> div:nth-child(4) > div');
        await ChoseDate(2,start, st_y, st_m);

        await page.click('#tab2>div:nth-child(2)>div>form>div:nth-child(5)>div');
        await ChoseDate(2,end, en_y, en_m);
        return;
        await page.click('#label_groupbyprefix > span');
        await page.click('#formRedPocketReportDetail > div:nth-child(9) > div > button.sui-btn.btn-primary');
        await page.waitFor(() => document.querySelector("#redPocketReportTb > thead > tr > th.ng-scope"));
        await console.log('取得資料');

        result[SUB_web].push( await page.evaluate(() =>{
          var num_tr = document.querySelector("#redPocketReportTb > tbody").rows.length;
          // var pref = document.querySelector("#redPocketReportTb > tbody > tr").rows[1].innerHTML;
          var prefix = [];
          var payout = [];
          var get_prefix;
          for(var i = 0; i < (num_tr-2); i++){
            var cells = document.querySelector("#redPocketReportTb > tbody").rows.item(i).cells;
            get_prefix = cells.item(1).querySelector('div > span').textContent;
            if(get_prefix == 'zwn'){
              prefix[i] = 'zwns';
            }
            else if(get_prefix == 'b13'){
              prefix[i] = 'b131';
            }
            else if(get_prefix == '336'){
              prefix[i] = '3368';
            }
            else if(get_prefix == 'axp'){
              prefix[i] = 'axpj';
            }
            else{
              prefix[i] = get_prefix;
            }
            payout[i] = cells.item(4).querySelector('div > span').textContent;

            
          }
          return {prefix,payout};
        }))
        console.log(result[SUB_web]);
      }

      //比賽報表
      if(SUB_web == 'AGIN_comp'){
        //選擇比賽類型
        await page.click('#formCompReportDetail > div:nth-child(1) > div > span');
        await page.click('body>span>span>span>ul>li:nth-child(3)');

       
        //開始時間
        await page.click('#formCompReportDetail > div:nth-child(3) > div');
        await ChoseDate(2,start, st_y, st_m);

        //結束日期
        await page.click('#formCompReportDetail > div:nth-child(4) > div');
        await ChoseDate(2,end, en_y, en_m);

        await page.click('#label_groupbyprefix > span');
        await page.waitFor(2000);
        await page.click('#tab1 > div.search-area > div.other-area > div:nth-child(8) > div > button.sui-btn.btn-primary');
        await page.waitFor(() => document.querySelector('#compreportTb > tbody > tr'));

        result[SUB_web].push(await page.evaluate(() => {
          var payout = 0;
          var ifdata = document.querySelector('#compreportTb > tbody > tr > td').innerText;
          if(ifdata == '(^o^),無記錄!'){
            var payout = 0;
          }
          else{
            var num_tr = document.querySelector("#compreportTb > tbody").rows.length;
            var prefix = [];
            var payout = [];
            var get_prefix = '';
            for(var i = 0; i <= (num_tr-3); i++){
              get_prefix = document.querySelector("#compreportTb > tbody").rows.item(i).cells.item(1).querySelector('div > span').textContent;
              if(get_prefix == 'zwn'){
                prefix[i] = 'zwns';
              }
              else if(get_prefix == 'b13'){
                prefix[i] = 'b131';
              }
              else if(get_prefix == '336'){
                prefix[i] = '3368';
              }
              else if(get_prefix == 'axp'){
                prefix[i] = 'axpj';
              }
              else{
                prefix[i] = get_prefix;
              }
              payout[i] = parseFloat(document.querySelector("#compreportTb > tbody").rows.item(i).cells.item(5).querySelector('div > span').textContent.replace(/,/g,""));
            } 

            return{prefix,payout};
          }
         
        }))
        

      }

      //捕魚活動
      if(SUB_web == 'AGIN_hunterevt'){
        //開始日期
        await page.click('#formHunterMember > div:nth-child(2) > div');
        await ChoseDate(2,start, st_y, st_m);
        //結束日期
        await page.click('#formHunterMember > div:nth-child(3) > div');
        await ChoseDate(2,end, en_y, en_m);

        await page.click('#formHunterMember > div:nth-child(11) > div > button.sui-btn.btn-primary');
        await page.waitFor(3000);

        result[SUB_web] = await page.evaluate(() => {
          var payout = 0;
          var ifdata = document.querySelector('#hunterMemberTb > tbody > tr > td').innerText;
          if(ifdata == '(^o^),無記錄!'){
            var payout = 0;
          }
          else{
            var num_tr = document.querySelector("#hunterMemberTb > tbody").rows.length;
            
            var get_data=[];
            var get_prefix = '';
            for(var i = 0; i <= (num_tr-3); i++){
              var get_user = document.querySelector("#hunterMemberTb > tbody").rows.item(i).cells.item(1).querySelector('div > span').textContent;
              var get_prefix = get_user.substr(0,3);
              var payout = parseFloat(document.querySelector("#hunterMemberTb > tbody").rows.item(i).cells.item(3).querySelector('div > span').textContent.replace(/,/g,""));
              //console.log(payout);
              if(get_prefix == 'zwn'){
                if(!get_data['zwns']){
                  get_data['zwns'] = payout
                }
                else{
                  get_data['zwns'] += payout
                }
              }
              else if(get_prefix == 'b13'){
                if(!get_data['b131']){
                  get_data['b131'] = payout
                }
                else{
                  get_data['b131'] += payout
                }
              }
              else if(get_prefix == '336'){
                if(!get_data['3368']){
                  get_data['3368'] = payout
                }
                else{
                  get_data['3368'] += payout
                }
              }
              else if(get_prefix == 'axp'){
                if(!get_data['axpj']){
                  get_data['axpj'] = payout
                }
                else{
                  get_data['axpj'] += payout
                }
              }
              else{
                if(!get_data[get_prefix]){
                  get_data[get_prefix] = payout
                }
                else{
                  get_data[get_prefix] += payout
                }
              }

            } 
            var data = []
            for(pre in get_data){
                data.push({'prefix': pre, 'payout': get_data[pre]})
               
            }
            
          }
          
          return data
        })
        
      }

      //元宵彩票
      if(SUB_web == 'AGIN_lanternLottery'){
        await page.goto(url+SUB_data.url+'#!#tab3');
        await page.evaluate(() => {
          document.querySelector("#contentBody > div:nth-child(5) > div.sui-layout.content-group > div > ul > li:nth-child(1)").setAttribute('class','');
          document.querySelector("#contentBody > div:nth-child(5) > div.sui-layout.content-group > div > ul > li:nth-child(3)").setAttribute('class','active');
          document.getElementById('tab1').setAttribute('class','tab-pane ng-scope');
          document.getElementById('tab3').setAttribute('class','tab-pane ng-scope active');
        })

        //選擇開始日期
        await page.click('#re-begintime');
        await ChoseDate(1,st_dt, st_y, st_m);

        //選擇結束日期
        await page.click('#re-endtime');
        await ChoseDate(1,end, en_y, en_m);

        //模糊查詢
        await page.click('#re-fz');

        //抓各站資料
        for(var web of value.webs){
          console.log(web);
          await page.evaluate(() => {
            document.querySelector('#re-username').value="";
          })
          await page.waitFor(2000);
          //輸入各站
          await page.click('#re-username');
          await page.type('#re-username', web.toLowerCase());
          await page.waitFor(2000);

          //查詢
          await page.click('#formRecord > div:nth-child(14) > div > button.sui-btn.btn-primary');
          await page.waitFor(5000);
          await page.waitFor(() => document.querySelector('#recordTb > tbody > tr'));
          result[SUB_web].push(await page.evaluate((prefix) => {
            var payout = 0;
            var ifdata = document.querySelector('#recordTb > tbody > tr > td').innerText;
            if(ifdata != '(^o^),無記錄!'){
              payout = parseFloat(document.querySelector('#recordTb > tbody > tr:last-child > td:nth-child(9) > div > span').innerText.replace(/,/g,""));
              console.log(payout)
            }
            
            return {prefix, payout};
          },web))
          console.log('get')
        }


      }
      console.log(result[SUB_web]);
      
    }
    //儲存暫存檔
    await console.log(tmp_log_line+"儲存暫存檔");
    await fs.writeFileSync(tmp_log_line, JSON.stringify(result));

    await browser.close();

    return result;

    
    //選擇日期
    async function ChoseDate(type,date,year,month){

      switch(type){
        case 1:
          var today = new Date();
          today.setDate(today.getDate()-1);
          var rep_m = today.getMonth();
          var rep_y = today.getFullYear();
          //搜尋的月份不等於該月份
          if(month != rep_m){
            var diff = ((rep_y-year)*12) + (rep_m - month);

            if(diff != 0){
              for(var i = 1; i <= diff; i++){
                await page.click('#ui-datepicker-div > div.ui-datepicker-header.ui-widget-header.ui-helper-clearfix.ui-corner-all > a.ui-datepicker-prev.ui-corner-all');
              }
            }
            
          }
 
          break;
        case 2:
          await page.click('.ui-datepicker-year');
          await page.select('.ui-datepicker-year',year);
          await page.click('.ui-datepicker-month');
          await page.select('.ui-datepicker-month',month);
          break;
        default:
         console.log('No type!');
         break;
      }
       
      await page.evaluate((date,month) => {
        var en_dt = new Date(date);
        var en_d = en_dt.getDate();
        //console.log(en_d);
        var days = document.querySelectorAll("#ui-datepicker-div > table > tbody > tr > td > a");
        for(var day of days){
          if(day.innerText == en_d){
            day.click();
          }
        }
        document.querySelector('button.ui-datepicker-close.ui-state-default.ui-priority-primary.ui-corner-all').click();
        
      },date,month)

     
    }
}

