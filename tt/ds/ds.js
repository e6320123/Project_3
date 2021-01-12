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
    await page.goto('https://agcny.ds781.pw/login',{waitUntil:'domcontentloaded'});

    let obj = new LoginProcess(page,browser) ;
    await obj.login(sd,ed) ;
    if(iseveryday == undefined)
    {
        let tmp = await obj.getdata(sd,ed) ;
        await obj.savedata(sd,tmp) ;
    }
    else
    {
        arreverydate= myModule.everydaynoadd(sd,ed,0) ;
        for(let arrdate of arreverydate){
            let tmp = await obj.getdata(arrdate.sd,arrdate.ed) ;
            await obj.savedata(arrdate.sd,tmp) ;
        }
    }

    await browser.close();
    return JSON.stringify(obj.output());
};

function LoginProcess(page,browser)
{
    this.data = {} ;
    this.page = page ;
    this.browser = browser ;
}
LoginProcess.prototype.login = async function(sd,ed)
{
    await this.page.waitFor(() => document.querySelector('body > app-root > app-login > section > div > mat-card > form > mat-card-actions > button'));
    await this.page.waitFor(2000);

    await this.page.type('#mat-input-0','BOSSCS') ;
    await this.page.type('#mat-input-1','qqqw7eag') ;
    await this.page.waitForNavigation();
    await this.page.waitFor(1000);


    await this.page.waitFor(() => document.querySelector('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav > mat-nav-list > spock-submenu:nth-child(7) > mat-list-item > div > div.mat-list-item-ripple.mat-ripple'));
    await this.page.waitFor(1000);
    await this.page.click('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav > mat-nav-list > spock-submenu:nth-child(7) > mat-list-item > div > div.mat-list-item-ripple.mat-ripple');
    await this.page.waitFor(1000);
    await this.page.click('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav > mat-nav-list > spock-submenu:nth-child(7) > div > mat-list-item:nth-child(2) > div');
    await this.page.waitForNavigation();
    await this.page.waitFor(1000);
    await this.page.waitFor(() => document.querySelector('#cdk-accordion-child-1 > div > div > button:nth-child(4)'));
    await this.page.waitFor(1000);
    await this.page.type('#mat-input-4',sd) ;
    await this.page.type('#mat-input-5',ed) ;
    await this.page.type('#mat-input-6','0') ;
    await this.page.type('#mat-input-7','0') ;
    await this.page.type('#mat-input-8','0') ;
    await this.page.type('#mat-input-9','23') ;
    await this.page.type('#mat-input-10','59') ;
    await this.page.type('#mat-input-11','59') ;

    //await this.page.type('#mat-input-11','59') ;
    await this.page.waitFor(1000);
    await this.page.click('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav-content > div > section > app-agent-period > div > mat-paginator > div > div.mat-paginator-page-size.ng-star-inserted > mat-form-field > div > div.mat-form-field-flex > div');
    await this.page.waitFor(1000);
    await this.page.click('.mat-select-content .mat-option:last-child span');
    //await this.page.click('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav-content > div > section > app-agent-period > div > div > div > div > button:nth-child(5)');
    //await this.page.waitFor(1000);
    /*await this.page.click('#mat-select-2 > div > div.mat-select-value');
    await this.page.waitFor(1000);
    await this.page.click('#mat-option-122 > span');
    await this.page.waitFor(1000);*/
}
LoginProcess.prototype.getdata = async function(sd,ed)
{
    // await this.page.evaluate( () => document.getElementById("mat-input-3").value = "") ;
    // await this.page.evaluate( () => document.getElementById("mat-input-4").value = "") ;
    // await this.page.type('#mat-input-4',sd) ;
    // await this.page.type('#mat-input-5',ed) ;
    // await this.page.waitFor(1000);
    await this.page.click('#cdk-accordion-child-1 > div > div > button:nth-child(4)') ;
    await this.page.waitFor(2000);
    const result = await this.page.evaluate(() => {
        let data = [];
        let games = document.querySelectorAll('body > app-root > app-layout > section > mat-sidenav-container > mat-sidenav-content > div > section > app-agent-period > div > mat-table > mat-row');
        console.log(games) ;
        for (var game of games){
            if(game.querySelector('mat-cell:nth-child(1)') == null) continue ;
            //let time = game.querySelector('td:nth-child(7) > div > a').getAttribute('href');
            let site = game.querySelector('mat-cell:nth-child(1)').innerText.trim();
            let money = game.querySelector('mat-cell:nth-child(8)').innerText.trim();
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