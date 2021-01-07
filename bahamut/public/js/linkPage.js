var news_obj = [
    {fname:"dxi",imgName:"1",title:"《勇者鬥惡龍XI S》體驗版近期公開！《魔導少年》真島浩設計服裝同步發表",content:` 日本 Square Enix 預定於 2019 年 9 月 27日在 Nintendo Switch 主機上推出，將之前在 PS4／N3DS 等平台推出的人氣
    RPG《DQXI》重新加強移植的《勇者鬥惡龍XI S 尋覓逝去的時光 Definitive Edition》（ドラゴンクエストXI
    過ぎ去りし時を求めてS），宣布將於近期推出遊戲體驗版讓玩家們搶先試玩！本作為一款將之前於 PS4／N3DS 主機上推出的《DQXI》給重新移植到 Switch
    主機上重新推出，並追加角色語...`},
    {fname:"zinyu",imgName:"2",title:"【評測】與其奔波勞碌廝殺拚命，不如回《神鵰俠侶2》做比翼雙飛",content:`由中國完美時空出品的重大IP製作《神鵰俠侶2》，歷經近一年的封測與調校，於7月底正式開啟內地全平台公測。
    細緻畫質任君選擇與之前版本不同的，主要在於畫面細膩度與3D視角的自由移動。目前大多數的手遊雖然已經可以自由切換 ...`},
    {fname:"bee",imgName:"bee",title:`人氣PC圖像益智猜謎問答手機移植版《Koongya Catch
    Mind》8月8日韓國雙平台同步推出`,content:`韓國 Netmarble（網石遊戲）預定於 2019 年在韓國手機平台上推出的圖像益智猜謎問答遊戲《Koongya Catch Mind》（쿵야 캐치마인드），正式宣布將決定於
    8 月 8 日起在韓國 App Store／Google Play 推出上架！本作為一款將 200....`},
    {fname:"cod",imgName:"1",title:`《決勝時刻：現代戰爭》多人對戰宣傳預告曝光！跨平台連線公測日期同步公布`,content:`由 Infinity Ward 所開發的《決勝時刻：現代戰爭》在今年 5 月首次曝光後便獲得了不小的關注，而隨著正式的發售日期已確立為 10 月 25
    日後，更是讓不少系列粉絲更為期待。而官方近期也釋出了新的遊戲展示預告，讓玩家一窺該作的多..`},
    {fname:"wc3",imgName:"wc3",title:`《魔獸爭霸 3》重製版新資訊曝光！索爾、泰蘭妲及多個單位高畫質遊戲模組亮相`,content:`上曝光後，不少死忠玩家便都在等待該款作品再次以高品質的重製再次登場，不過官方至今依然沒有給出明確的上市日期，僅表示遊戲預計於今年正式登場，想必是讓不少人等的相當痛苦吧？...`},
    {fname:"cadin",imgName:"2",title:`《跑跑卡丁車》世界爭霸賽國家代表決賽 4名國家代表將赴韓爭奪世界冠軍`,content:`遊戲橘子今年首度舉辦的《跑跑卡丁車》世界爭霸賽，於昨（4）日在世貿漫畫博覽會現場舉行
    2019《跑跑卡丁車》世界爭霸賽國家代表最終戰，來自各方實力強勁的代表隊在經過一連串精采絕倫的賽事後，最終由「爆哥」、「睏平」...`}
]

var list = 
    '[{"inx":"0" ,"platform":"Online", "name":"魔獸世界：決戰艾澤拉斯" , "imgsrc":"wow.png", "imgsize":"120x120"},' +
    '{"inx":"1" ,"platform":"PC",     "name":"帝國：全軍破敵" ,        "imgsrc":"etw.jpg", "imgsize":"100x130"},' +
    '{"inx":"2" ,"platform":"PC",    "name":"刺客教條 2" ,             "imgsrc":"ac2.jpg", "imgsize":"100x130"},' +
    '{"inx":"3" ,"platform":"PC",    "name":"全軍破敵：三國" ,         "imgsrc":"ttw.jpg", "imgsize":"100x130"},' +
    '{"inx":"4" ,"platform":"PS4",    "name":"惡靈古堡 2 重製版" ,      "imgsrc":"bio2.png", "imgsize":"100x130"},' +
    '{"inx":"5" ,"platform":"Online", "name":"暗黑破壞神 3：奪魂之鐮" , "imgsrc":"d3.png", "imgsize":"120x120"},' +
    '{"inx":"6" ,"platform":"PS4",    "name":"惡魔獵人 5" ,            "imgsrc":"dmc5.jpg", "imgsize":"100x130"},' +
    '{"inx":"7" ,"platform":"PS4",    "name":"漫威蜘蛛人" ,            "imgsrc":"spm.png",  "imgsize":"100x130"},' +
    '{"inx":"8" ,"platform":"NS",    "name":"勇者鬥惡龍 XI S 尋覓逝去的時光 – Definitive Edition" ,"imgsrc":"dq.png", "imgsize":"100x130"},' +
    '{"inx":"9","platform":"PS4",    "name":"魔物獵人 世界" ,         "imgsrc":"mons.png","imgsize":"100x130" },' +
    '{"inx":"10","platform":"PC",    "name":"巫師 3：狂獵" ,          "imgsrc":"witch.jpg","imgsize":"100x130" },' +
    '{"inx":"11","platform":"NS",    "name":"寶可夢 劍" ,             "imgsrc":"poke.png","imgsize":"100x130" },' +
    '{"inx":"12","platform":"NS",    "name":"薩爾達傳說 曠野之息" ,    "imgsrc":"zelda.png","imgsize":"100x130" },' +
    '{"inx":"13","platform":"Online", "name":"佩里亞編年史" ,          "imgsrc":"peria.jpg","imgsize":"120x120" },' +
    '{"inx":"14" ,"platform":"NS",     "name":"哆啦 A 夢 牧場物語" ,    "imgsrc":"dora.png", "imgsize":"100x130"}]';
 
var search_str = `<p style="border-bottom:2px solid gray;" >遊戲搜索欄</p>`;
var util = new Util(); 


var all_xmp2 = "";
news_obj.forEach(objects => {
    var xmp_ele = getEle("xmp_linkTable2");
    xmp_ele = xmp_ele.replace(/\*fname\*/g, objects.fname);
    xmp_ele = xmp_ele.replace(/\*imgName\*/g, objects.imgName);
    xmp_ele = xmp_ele.replace(/\*title\*/g, objects.title);
    xmp_ele = xmp_ele.replace(/\*content\*/g, objects.content); 
    all_xmp2 += xmp_ele;
}); 


var hR = getEle("xmp_linkTable") + all_xmp2 + getEle("xmp_linkTable3"); 

putEle("R_content",hR)

function getEle(id){
    return util.getSpan(document,id).innerHTML;
}
function getVal(id){
    return util.getSpan(document,id).value;
}

function putEle(id,str){
    document.getElementById(id).innerHTML = str;
}

//洗亂陣列
function shuffle(arr) {
    var i,j,temp;
    for (i = arr.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }
    return arr;
};

var obj = JSON.parse(list);

//初始搜索欄遊戲全載入
var all_ele = "";
obj.forEach(objects => {
    var xmp_ele = getEle("xmp_smallFrame");
    xmp_ele = xmp_ele.replace(/\*inx\*/g, objects.inx);
    xmp_ele = xmp_ele.replace(/\*imgsize\*/g, objects.imgsize);
    xmp_ele = xmp_ele.replace(/\*imgsrc\*/g, objects.imgsrc);
    xmp_ele = xmp_ele.replace(/\*platform\*/g, objects.platform);
    xmp_ele = xmp_ele.replace(/\*name\*/g, objects.name);
    all_ele += xmp_ele;
});
putEle("L_content",search_str + all_ele)

//xmp轉成html並回傳
function xmp_2_ele(id,obj,count){
    var xmp_ele = getEle(id);
    xmp_ele = xmp_ele.replace(/\*inx\*/g, obj[count].inx);
    xmp_ele = xmp_ele.replace(/\*imgsize\*/g, obj[count].imgsize);
    xmp_ele = xmp_ele.replace(/\*imgsrc\*/g, obj[count].imgsrc);
    xmp_ele = xmp_ele.replace(/\*platform\*/g, obj[count].platform);
    xmp_ele = xmp_ele.replace(/\*name\*/g, obj[count].name);
    return xmp_ele;
}

// 點搜尋鈕
$("#search>img").click(function () {
    var allEle = search_str; 
    var siArry = [];  
    var ch = getVal("search_column");
    for (var i = 0; i < obj.length; i++) { 
        var game = obj[i].name; 
        if (game.indexOf(ch) > -1 && ch != "") {  
            var xmp_ele = xmp_2_ele("xmp_smallFrame",obj,i);
            siArry.push(xmp_ele); 
        }
    }
    shuffle(siArry);
    var len = siArry.length;
    for (var i = 0; i < len; i++) {
        allEle += siArry[i];
    } 
    putEle("L_content",allEle)
})

// 按enter搜尋
$("body").keydown(function (e) {  
    if (e.keyCode == 13) {
        var allEle = search_str;   
        var ch = getVal("search_column");
        for (var i = 0; i < obj.length; i++) { 
            var game = obj[i].name;
            if (game.indexOf(ch) > -1 && ch != "") { 
                var xmp_ele = xmp_2_ele("xmp_smallFrame",obj,i);
                allEle += xmp_ele; 
            }
        }  
        putEle("L_content",allEle) 
    }
})  
 
// 點平台
function navShowL(platform) {
    var result = "";
    var navch = platform;
    for (var i = 0; i < obj.length; i++) {
        var game = obj[i].platform;
        if (game.indexOf(navch) > -1) {    
            var xmp_ele = xmp_2_ele("xmp_smallFrame",obj,i);
            result += xmp_ele;
        }
    }
    $("#L_content").append(result);
} 
function nav_set_clk(num,platform){
    $("ul>li").eq(num).click(function () {
        $("#L_content").empty().append(search_str);
        navShowL(platform);
    })
}
nav_set_clk(1,"Online");
nav_set_clk(2,"PC");
nav_set_clk(3,"PS4");
nav_set_clk(4,"NS"); 

$("ul>li").eq(0).click(function () {
    //首頁L------------------------------------------------------- 
    $("#L_content").empty().append(search_str); 
    navShowL("PC");
    navShowL("PS4");
    navShowL("Online");
    navShowL("NS");
    //首頁R------------------------------------------------------- 
    putEle("R_content",hR); 
})


