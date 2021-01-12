const puppeteer = require('puppeteer');
const fs = require('fs');
const chrome_exe = String.raw`${process.env["ProgramFiles(x86)"]}\Google\Chrome\Application\chrome.exe`;
const user_data_path = String.raw`${process.env.LocalAppData}\Google\Chrome\User Data\Default`;
const myModule = require('./MyDate');

var browser, page;
async function run () {
    var sd = process.argv[2] ;
    var ed = process.argv[3] ;
    let iseveryday = process.argv[4] ;
    const browser = await puppeteer.launch({
        headless: false,
        //devtools: true,
        userDataDir: user_data_path,
        executablePath: chrome_exe
    });
    const page = await browser.newPage();
    page.setViewport({ width: 1920, height: 1080 });
    await page.goto('http://www.ebet2018.com/login',{waitUntil:'domcontentloaded'});
    await page.waitFor(2000);
    const islogin = await page.evaluate(() => {
        let data = '' ;
        if(document.querySelector('#dataId'))
            data = document.querySelector('#dataId').innerText.trim();
        return data;
    });
    let obj = new LoginProcess(page,browser) ;
    // if(islogin=='数据后台')
    await obj.login() ;
    await obj.standby() ;
    if(iseveryday == undefined)
    {
        let tmp = await obj.getdata(sd,ed) ;
        await obj.savedata(sd,tmp) ;
    }
    else
    {
        arreverydate= myModule.everyday(sd,ed,1) ;
        for(let arrdate of arreverydate){
            let tmp = await obj.getdata(arrdate.sd,arrdate.ed) ;
            await obj.savedata(arrdate.sd,tmp) ;
        }
    }
    await page.click('body > div:nth-child(1) > div > div > div:nth-child(1) > div > div.layout-logout > div > div.ivu-dropdown > div.ivu-dropdown-rel > button > span');
    await page.waitFor(1000);
    await page.click('body > div:nth-child(1) > div > div > div:nth-child(1) > div > div.layout-logout > div > div.ivu-dropdown > div.ivu-select-dropdown > ul > li:nth-child(2)') ;
    await page.waitFor(1000);
    await page.click('body > div:nth-child(13) > div.ivu-modal-wrap > div > div > div.ivu-modal-footer > div > button') ;
    await page.waitForNavigation();
    await browser.close();
    return JSON.stringify(obj.output());
};

function LoginProcess(page,browser)
{
    this.data = {} ;
    this.page = page ;
    this.browser = browser ;
}
LoginProcess.prototype.login = async function()
{
    await this.page.waitFor(2000);
    await this.page.type('#userInput > input','BGECS') ;
    await this.page.type('#pswInput > input','7rAwGp5X3D70') ;
    await this.page.waitFor(1000);
    await this.page.click('#Div');
    await this.page.waitForNavigation();
    await this.page.waitFor(5000);
}
LoginProcess.prototype.standby = async function()
{
    await this.page.waitFor(() => document.querySelector('#game > div'));
    await this.page.waitFor(2000);
    await this.page.click('#game > div');
    await this.page.waitFor(1000);
    await this.page.click('#reportToolList > a');
    await this.page.waitFor(() => document.querySelector('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(1) > div > div > div > div.ivu-date-picker-rel > div > input'));
}
LoginProcess.prototype.getdata = async function(sd,ed)
{
    let searchday = sd+" 00:00:00 - "+ed+" 00:00:00" ;
    await this.page.waitFor(3000);
    //清空
    // await this.page.click('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(1) > div > div > div > div.ivu-date-picker-rel > div > input');
    // await this.page.waitFor(1000);
    // await this.page.click('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(1) > div > div > div > div.ivu-select-dropdown > div:nth-child(2) > div > div > div.ivu-picker-confirm > button.ivu-btn.ivu-btn-ghost.ivu-btn-small');
    // await this.page.waitFor(1000);
    //清空
    await this.page.$eval('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(1) > div > div > div > div.ivu-date-picker-rel > div > input',input => input.value='');
    await this.page.waitFor(1000);
    await this.page.type('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(1) > div > div > div > div.ivu-date-picker-rel > div > input',searchday) ;
    await this.page.waitFor(1000);
    await this.page.click('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(3) > form > div:nth-child(7) > div > button');
    await this.page.waitFor(() => document.querySelector('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(4) > div.ivu-table-wrapper > div > div.ivu-table-fixed > div.ivu-table-fixed-body > table > tbody > tr:nth-child(1) > td:nth-child(1) > div > span'));
    await this.page.waitFor(3000);
 
    const result = await this.page.evaluate(() => {
        let data = [];
        let games = document.querySelectorAll('body > div:nth-child(1) > div > div > div.row-flex.ivu-row-flex > div:nth-child(2) > div > div > div > div:nth-child(4) > div.ivu-table-wrapper > div > div.ivu-table-fixed > div.ivu-table-fixed-body > table > tbody > tr');         
        for (var game of games){
            //if(game.querySelector('td:nth-child(7) > div > a') == null) continue ;
            let site = game.querySelector('td:nth-child(3) > div > span').innerHTML.trim();
            let money;
            if(game.querySelector('td:nth-child(12) > div > a') !== null)
                money = game.querySelector('td:nth-child(12) > div > a').innerHTML.trim();
            else
                money = '0';

            data.push({site, money});
        }
        return data;
    });
    return result ;
}
LoginProcess.prototype.savedata = async function(datetime,arrtmp)
{
    this.data[datetime] = arrtmp ;
}
LoginProcess.prototype.output = function()
{
    return this.data ;
}

run().then((value) => {
    console.log(value); // Success!
});
/*process.argv.forEach(function(val, index, array) {
  console.log(index + ': ' + val);
});*/
 /*change_value=function(query,value) {
      var select=document.querySelector(query);
      select.click();
      select.value=value;
      var event = new Event('compositionend');
      select.dispatchEvent(event);
    }*/