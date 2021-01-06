//##########################價錢區############################ 
var pay = 33000;
var bh = 16000;
var shyDi = 2029;
var carDi = 3268;
var lunch = 95;
//###########################################################
var util = new Util();
var listener = new ListenEvent();
var fieldNum = 5;
var saveObj = {};
for (let i = 0; i < 25; i++) {
    saveObj[i] = {"str":"nodata","num":5};
}
var subEvent = false;   //判斷是否是減少欄位事件
//讀取localStorage
var saveData = localStorage.getItem("Financial");
saveData = JSON.parse(saveData);
if(saveData != null) saveObj = saveData;
//隱藏舊存檔按鈕
right.style.display = "none";
right2.style.display = "none";
var slide_load_flag = false;

showRight();
chgField();
initAddEvent();

//依月份調整讀取檔案
getEle("slide_load").value = new Date().getMonth()*1+1;
getEle("slide_load_value").innerText = new Date().getMonth()*1+1;
show_slide_num2() 

//印出存檔按鈕
function showRight(){
    for (var i = 1; i <= 24; i++) {
        if(i>=13){
            var n = i-12
            right2.innerHTML += save_proto.innerHTML.replace(/\*txt\*/g,"Detail_"+n).replace(/\*/g,i);
            continue;
        }
        right.innerHTML += save_proto.innerHTML.replace(/\*txt\*/g,i).replace(/\*/g,i);
    }
}
function getEle(ele){
    return util.getSpan(document,ele);
}
function chgField(){
    //計算現在欄位數
    var nowFieldNum = 0;
    if(inputArea.innerHTML != "") nowFieldNum = document.getElementsByName("input").length;
    //儲存欄位資料
    var dataAry = [];   //數字
    var titleAry = [];  //標題
    if(subEvent) nowFieldNum--;   //判斷若是減少欄位事件,則拿掉最後一筆資料
    for (var i = 1; i <= nowFieldNum; i++) {
        var data = util.getSpan(document,"i"+i).value;
        dataAry.push(data);
        var title = util.getSpan(document,"t"+i).innerText;
        titleAry.push(title);
    }
    //清除欄位與列表
    showList.innerHTML = "";
    inputArea.innerHTML = "";

    //新增輸入欄位與運算列表
    for (var i = 1; i <= fieldNum; i++) {
        var list = util.getSpan(document,"list_proto");
        var proto = util.getSpan(document,"input_proto");
        showList.innerHTML += list.innerHTML.replace(/\*/g,i);
        inputArea.innerHTML += proto.innerHTML.replace(/\*/g,i);
    }
    //載回欄位資料 
    var count = dataAry.length;
    for (var i = 1; i <= count; i++) {
        var data = dataAry.shift();
        if(data != "") util.getSpan(document,"i"+i).value = data;
        var title = titleAry.shift();
        if(title.trim() != "") util.getSpan(document,"t"+i).innerText = title;
    }
    //掛上數字欄改變事件
    var inputSet = document.getElementsByName("input");
    for (var i = 0; i < inputSet.length; i++){
        listener.addSelectOnChange("input"+i,inputSet[i],this,null);
    }
    //掛上標題欄改變事件
    var inputTitleSet = document.getElementsByName("inputTitle");
    for (var i = 0; i < inputTitleSet.length; i++){
        listener.addSelectOnChange("inputTitle"+i,inputTitleSet[i],this,null);
    }
    equal.click();
} 
function initAddEvent(){
    listener.addOnClick("equal",util.getSpan(document,"equal"),this,null);
    listener.addOnClick("clear",util.getSpan(document,"clear"),this,null);
    listener.addOnClick("addField",util.getSpan(document,"addField"),this,null);
    listener.addOnClick("subField",util.getSpan(document,"subField"),this,null);
    for (var i = 1; i <= 24; i++) {
        listener.addOnClick("save_"+i,util.getSpan(document,"save_"+i),this,null);
        listener.addOnClick("load_"+i,util.getSpan(document,"load_"+i),this,null);
    }
    //掛上鍵盤控制事件
    listener.addKeyPress("press",window,this,null);
    //新存檔讀取
    listener.addOnClick("n_save",util.getSpan(document,"n_save"),this,null);
    listener.addOnClick("n_load",util.getSpan(document,"n_load"),this,null);
    //左右鍵
    listener.addOnClick("l_btn",util.getSpan(document,"l_btn"),this,null);
    listener.addOnClick("r_btn",util.getSpan(document,"r_btn"),this,null);
    listener.addOnClick("ll_btn",util.getSpan(document,"ll_btn"),this,null);
    listener.addOnClick("rr_btn",util.getSpan(document,"rr_btn"),this,null);
    //常用紀錄
    var clickEle = document.getElementsByName("click");
    var leng = clickEle.length;
    for (var i = 0; i < leng; i++) {
        var id = clickEle[i].id;
        var evtName = id
        listener.addOnClick(evtName,util.getSpan(document,id),this,null);
    }
}
function quick_input(input_num,title){
    for (var i = 1; i <= fieldNum; i++) {
        var l_id = "l"+i;
        var i_id = "i"+i;
        var t_id = "t"+i;
        if(getEle(i_id).value.trim() == pay && input_num == pay) break;
        if(getEle(i_id).value.trim() == bh && input_num == bh) break;
        if(getEle(i_id).value.trim() == shyDi && input_num == shyDi) break;
        if(getEle(i_id).value.trim() == carDi && input_num == carDi) break;
        if(getEle(l_id).innerText.trim() == ""){
            getEle(i_id).value = input_num;
            getEle(t_id).innerText = title;
            equal.click();
            break;
        }
    }
}
function listenCenter(eventName,listenData){
    var div = listenData.div;      //loginbtn
    var obj = listenData.object;   //null
    var e = listenData.e;          //onclicke

    if(eventName=="cost_d") {
        var else_filed_num = saveObj[getEle("slide_load_value").innerText*1+12]["num"];
        var all_cost = 0;
        for (var i = 2; i <= else_filed_num; i++) { 
            if(saveObj[getEle("slide_load_value").innerText*1+12]["i"+i] == "") continue;
            all_cost += saveObj[getEle("slide_load_value").innerText*1+12]["i"+i]*1;
        }
        if(all_cost == 0) return alert("detail無紀錄");
        for (var i = 2; i <= fieldNum; i++) {
            if(getEle("i"+i).value.trim() == ""){
                getEle("t"+i).innerText ="Detail總花費";
                getEle("i"+i).value = all_cost;
                equal.click();
                break;
            }
        }
    }
    if(eventName=="add_d") {
        var detail = getEle("showTotal").innerText;
        if(detail.trim() == "" || detail.trim() == 0) return alert("沒有餘額可加");
        saveObj[getEle("slide_load_value").innerText*1+12]["str"] = "save" + getEle("slide_load_value").innerText*1+12;
        saveObj[getEle("slide_load_value").innerText*1+12]["t1"] = "剩餘金額";
        saveObj[getEle("slide_load_value").innerText*1+12]["l1"] = detail;
        saveObj[getEle("slide_load_value").innerText*1+12]["i1"] = detail;
        alert("ok");
        return;
    }
    if(eventName=="pay") {
        quick_input(pay,"薪水");
        return;
    }
    if(eventName=="bh") { 
        quick_input(bh,"家用");
        return;
    }
    if(eventName=="shyDi") {
        quick_input(shyDi,"學貸");
        return;
    }
    if(eventName=="carDi") {
        quick_input(carDi,"車貸");
        return;
    }
    if(eventName=="lunch") {
        quick_input(95,"午飯");
        return;
    }
    if(eventName=="ll_btn") {
        var val = getEle("slide_load").value * 1;
        if(val > 12) getEle("slide_load").value = val-12;
        show_slide_num2();
        return;
    }
    if(eventName=="rr_btn") {
        var val = getEle("slide_load").value * 1;
        if(val <= 12) getEle("slide_load").value = val+12;
        show_slide_num2();
        return;
    }
    if(eventName=="l_btn") {
        var val = getEle("slide_load").value * 1;
        if(val > 1) getEle("slide_load").value = val-1;
        show_slide_num2();
        return;
    }
    if(eventName=="r_btn") {
        var val = getEle("slide_load").value * 1;
        if(val < 24) getEle("slide_load").value = val+1;
        show_slide_num2();
        return;
    }
    //對應輸入數字事件
    var inputSet = document.getElementsByName("input");
    for (var i = 0; i < inputSet.length; i++) {
        if(eventName=="input"+i) {
            var str = getEle("i"+(i*1+1)).value + "";
            var exReg = /[0-9k\s]/;
            if(!exReg.test(str)) return getEle("i"+(i*1+1)).value = "";
            equal.click();
        }
    }
    //新存檔
    if(eventName=="n_save") {
        // var num = prompt("輸入存檔數字(1 or d1)：");
        var num = getEle("slide_save").value;
        if(num[0] == "D") num = num[1]*1 + 12;
        getEle("save_"+num).click();
    }
    //新讀取
    if(eventName=="n_load") {
        // var num = prompt("輸入讀取數字(1 or d1)：");
        var num = getEle("slide_load").value;
        if(num[0] == "D") num = num[1]*1 + 12;
        getEle("load_"+num).click();
    }
    //對應輸入標題事件
    var inputTitleSet = document.getElementsByName("inputTitle");
    for (var i = 0; i < inputTitleSet.length; i++) {
        if(eventName=="inputTitle"+i) {
            var k = i+1;    //修正對應id
            //輸入標題顯示在旁邊
            var inputTitle = util.getSpan(document,"it"+k).value;
            util.getSpan(document,"t"+k).innerHTML = inputTitle;
            util.getSpan(document,"it"+k).value = "";
        }
    }
    if(eventName=="equal"){
        var listSet = document.getElementsByName("list");
        var inputSet = document.getElementsByName("input");
        for (var i = 0; i < listSet.length; i++) {
            //欄1輸入
            var inputNum = inputSet[i].value;
            //k轉成千
            if(inputNum.indexOf("k") != -1) inputNum = inputNum.replace("k","000")*1;
            //把值列到清單 加正負號
            listSet[i].innerHTML = (i==0)? "&ensp;"+inputNum:"-"+inputNum;
            if(listSet[i].innerHTML.trim() == "-") listSet[i].innerHTML ="";
        }
        var total = listSet[0].innerHTML*1;
        for (var i = 1; i < listSet.length; i++) {
            if(listSet[i].innerHTML.trim() != "") total += listSet[i].innerHTML*1;
        }
        // if(total == 0) total = "&ensp;";
        util.getSpan(document,"showTotal").innerHTML = total;
    }
    if(eventName=="clear"){
        var listSet = document.getElementsByName("list");
        var inputSet = document.getElementsByName("input");

        for (var i = 0; i < inputSet.length; i++) {
            inputSet[i].value = "";
            listSet[i].innerHTML = "";
            var k = i+1; //對應id
            util.getSpan(document,"t"+k).innerHTML = "";
            util.getSpan(document,"it"+k).value = "";
        }

        showTotal.innerHTML = "&ensp;";
    }
    if(eventName=="addField"){
        fieldNum++;
        chgField();
    }
    if(eventName=="subField"){
        if(fieldNum >=5) fieldNum--;
        subEvent =true;
        chgField();
        subEvent =false;
    }
    //按鍵監聽事件
    if(eventName == "press"){
        // alert(e.keyCode);
        var keyChar = String.fromCharCode(e.keyCode);
        if(keyChar == "|") clear.click();
        if(keyChar == "+") addField.click();
        if(keyChar == "-") subField.click();
        
        if(keyChar == "[") ex_data();
        if(keyChar == "]") insert_field();
        if(keyChar == "\\") delete_field(); 

        //儲存12    (alt + 1~0 , - , =)
        // if(keyChar == "¡") save_1.click();
        // if(keyChar == "™") save_2.click();
        // if(keyChar == "£") save_3.click();
        // if(keyChar == "¢") save_4.click();
        // if(keyChar == "∞") save_5.click();
        // if(keyChar == "§") save_6.click();
        // if(keyChar == "¶") save_7.click();
        // if(keyChar == "•") save_8.click();
        // if(keyChar == "ª") save_9.click();
        // if(keyChar == "º") save_10.click();
        // if(keyChar == "–") save_11.click();
        // if(keyChar == "≠") save_12.click();
        //載入12    (shift + 1~0 , - , =)
        // if(keyChar == "!") load_1.click();
        // if(keyChar == "@") load_2.click();
        // if(keyChar == "#") load_3.click();
        // if(keyChar == "$") load_4.click();
        // if(keyChar == "%") load_5.click();
        // if(keyChar == "^") load_6.click();
        // if(keyChar == "&") load_7.click();
        // if(keyChar == "*") load_8.click();
        // if(keyChar == "(") load_9.click();
        // if(keyChar == ")") load_10.click();
        // if(keyChar == "_") load_11.click();
        // if(keyChar == "+") load_12.click();
        //儲存 Detail 12    (alt + Q~[])
        // if(keyChar == "œ") save_13.click();
        // if(keyChar == "∑") save_14.click();
        // if(keyChar == "´") save_15.click();
        // if(keyChar == "®") save_16.click();
        // if(keyChar == "†") save_17.click();
        // if(keyChar == "¥") save_18.click();
        // if(keyChar == "¨") save_19.click();
        // if(keyChar == "ˆ") save_20.click();
        // if(keyChar == "ø") save_21.click();
        // if(keyChar == "π") save_22.click();
        // if(keyChar == "“") save_23.click();
        // if(keyChar == "‘") save_24.click();
        //載入 Detail 12  
        // if(keyChar == "Q") load_13.click();
        // if(keyChar == "W") load_14.click();
        // if(keyChar == "E") load_15.click();
        // if(keyChar == "R") load_16.click();
        // if(keyChar == "T") load_17.click();
        // if(keyChar == "Y") load_18.click();
        // if(keyChar == "U") load_19.click();
        // if(keyChar == "I") load_20.click();
        // if(keyChar == "O") load_21.click();
        // if(keyChar == "P") load_22.click();
        // if(keyChar == "{") load_23.click();
        // if(keyChar == "}") load_24.click();
    } 
    //產生12個存讀事件 再+12
    for (var k = 1; k <= 24; k++) {
        if(eventName=="save_"+k){
            //若全部空白則不存檔
            var allEmpty = true;
            for (var i = 1; i <= fieldNum; i++) {
                if(util.getSpan(document,"l"+i).innerText.trim() != "") allEmpty = false;
                if(util.getSpan(document,"t"+i).innerText.trim() != "") allEmpty = false;
                if(util.getSpan(document,"i1").value.trim() != "") allEmpty = false;
            }
            if(allEmpty) return alert("都空白還存檔?");
            // if(confirm("即,確認存檔？"))
            if(saveObj[k].str == "nodata"){
                //提取各欄位資料
                for (let i = 1; i <= fieldNum; i++) {
                    console.log("newSave");
                    var tmpId = "l"+i;
                    saveObj[k][tmpId] = util.getSpan(document,tmpId).innerText;
                    tmpId = "t"+i;
                    saveObj[k][tmpId] = util.getSpan(document,tmpId).innerText;
                    tmpId = "i"+i;
                    saveObj[k][tmpId] = util.getSpan(document,tmpId).value;
                }
                saveObj[k].str = "save"+k;
                saveObj[k].num = fieldNum;
                console.log(left.innerHTML);
                localStorage.setItem("Financial", JSON.stringify(saveObj));
                alert("儲存成功！");
            }else{
                if(confirm("即將覆蓋已有的存檔,確認執行？")){
                    //提取各欄位資料
                    for (let i = 1; i <= fieldNum; i++) {
                        console.log("newSave");
                        var tmpId = "l"+i;
                        saveObj[k][tmpId] = util.getSpan(document,tmpId).innerText;
                        tmpId = "t"+i;
                        saveObj[k][tmpId] = util.getSpan(document,tmpId).innerText;
                        tmpId = "i"+i;
                        saveObj[k][tmpId] = util.getSpan(document,tmpId).value;
                    }
                    saveObj[k].str = "save"+k;
                    saveObj[k].num = fieldNum;
                    localStorage.setItem("Financial", JSON.stringify(saveObj));
                }
            }
        }
        if(eventName=="load_"+k){
            if(saveObj[k].str == "nodata"){
                if(slide_load_flag){
                    //清空所有資料
                    clear.click();
                    //設置存檔標題
                    var str = k>12? "D"+(k*1-12) : k;
                    util.getSpan(document,"saveTitle").innerHTML = "&ensp;存檔" + str;
                    //調整欄位數
                    var adj = 5 - fieldNum;
                    if(adj > 0){
                        for (var i = 0; i < adj; i++) 
                            addField.click();
                    }
                    if(adj < 0){
                        adj = Math.abs(adj);
                        for (var i = 0; i < adj; i++) 
                            subField.click();
                    } 
                    return;
                }else{
                    return alert("此欄位沒有存檔");
                }
            }
            //清空所有資料
            clear.click();
            //調整欄位數
            if(saveObj[k].num > fieldNum){
                var loop = saveObj[k].num - fieldNum;
                for (let i = 0; i < loop; i++) 
                    addField.click();
            }else{
                var loop = fieldNum - saveObj[k].num;
                for (let i = 0; i < loop; i++) 
                    subField.click();
            }
            //依序把資料填入各欄位
            for (let i = 1; i<= saveObj[k].num; i++) {
                var tmpId = "l"+i;
                document.getElementById(tmpId).innerHTML = saveObj[k][tmpId];
                tmpId = "t"+i;
                document.getElementById(tmpId).innerText = saveObj[k][tmpId];
                tmpId = "i"+i;
                document.getElementById("i"+i).value = saveObj[k]["i"+i];
            }
            fieldNum = saveObj[k].num;
            var listSet = document.getElementsByName("list");
            var inputSet = document.getElementsByName("input");
            // list資料填到輸入欄裡
            for (var i = 1; i <= listSet.length; i++) {
                util.getSpan(document,"i"+i).value = (i==1)?
                    util.getSpan(document,"l"+i).innerHTML.trim()
                    :util.getSpan(document,"l"+i).innerHTML.replace("-","");
            }
            initAddEvent();

            //掛上onChange事件
            var inputSet = document.getElementsByName("input");
            for (var i = 0; i < inputSet.length; i++){
                listener.addSelectOnChange("input"+i,inputSet[i],this,null);
            }
            //掛上onChange事件 (標題輸入欄)
            var inputTitleSet = document.getElementsByName("inputTitle");
            for (var i = 0; i < inputTitleSet.length; i++){
                listener.addSelectOnChange("inputTitle"+i,inputTitleSet[i],this,null);
            }
            equal.click();
            //設置存檔標題
            var str = k>12? "D"+(k*1-12) : k;
            util.getSpan(document,"saveTitle").innerHTML = "&ensp;存檔" + str;
        }
    }
}
function show_slide_num(){
    var value = getEle("slide_save").value;
    getEle("slide_save_value").innerText = value>12? "D"+(value-12) : value;
}
function show_slide_num2(){
    slide_load_flag = true;
    var value = getEle("slide_load").value;
    getEle("slide_load_value").innerText = value>12? "D"+(value-12) : value;
    getEle("slide_save").value = value;
    getEle("slide_save_value").innerText = value>12? "D"+(value-12) : value;
    n_load.click();
    slide_load_flag = false;
    if(value > 12){
        getEle("add_d").style.display = "none";
        getEle("cost_d").style.display = "none";
    }else{
        getEle("add_d").style.display = "";
        getEle("cost_d").style.display = "";
    }
}
var cnt;
var inputArea_evt = getEle("inputArea");
 function ex_data(drag) {
    cnt = 0
    inputArea_evt.addEventListener("click", handler, false);
    if(drag) return;
    toastr.success( "請點選2個要交換的輸入框" );
 }
 var ch1 = {"title":"","list":"","input":"","num":0};
 var ch2 = {"title":"","list":"","input":"","num":0};
 function handler(e){
    if(e.target.id == "inputArea") return; 
    cnt++;
    var num = e.target.id.replace("i","");
    if(cnt == 1){
        getEle("i"+num).style.border = "red solid 2px";
        ch1["title"] = getEle("t"+num).innerText;
        ch1["list"] = getEle("l"+num).innerText;
        ch1["input"] = getEle("i"+num).value; 
        ch1["num"] = num;
    }
    if(cnt >= 2){
 
        getEle("i"+ch1["num"]).style.border = ""; 
        ch2["title"] = getEle("t"+num).innerText;
        ch2["list"] = getEle("l"+num).innerText;
        ch2["input"] = getEle("i"+num).value; 
        ch2["num"] = num;

        getEle("t"+ch1["num"]).innerText = ch2["title"];
        getEle("l"+ch1["num"]).innerText = ch2["list"];
        getEle("i"+ch1["num"]).value = ch2["input"];

        getEle("t"+ch2["num"]).innerText = ch1["title"];
        getEle("l"+ch2["num"]).innerText = ch1["list"];
        getEle("i"+ch2["num"]).value = ch1["input"]; 

        inputArea_evt.removeEventListener("click", handler, false);
        getEle("i"+ch1["num"]).style.border = "";
        ch1 = {"title":"","list":"","input":"","num":0};
        ch2 = {"title":"","list":"","input":"","num":0};
        cnt = 0; 
    }
 }
function delete_field(drag){
    inputArea_evt.addEventListener("click", handler2, false);
    if(drag) return;
    toastr.error( "請點選要移除的欄位" );
}
function handler2(e){
    var num = e.target.id.replace("i","");
    for (var i = num; i < fieldNum; i++) { 
        var next = i*1+1; 
        getEle("t"+i).innerText = getEle("t"+next).innerText;
        getEle("l"+i).innerText = getEle("l"+next).innerText;
        getEle("i"+i).value = getEle("i"+next).value; 
    }
    inputArea_evt.removeEventListener("click", handler2, false);
    subField.click();
}
function insert_field(drag){
    inputArea_evt.addEventListener("click", handler3, false);
    if(drag) return;
    toastr.success( "點擊欄位上方插入新欄位" ); 
}
function handler3(e){
    addField.click();
    var num = e.target.id.replace("i","");
    for (var i = fieldNum; i > num; i--) { 
        var last = i*1-1; 
        getEle("t"+i).innerText = getEle("t"+last).innerText;
        getEle("l"+i).innerText = getEle("l"+last).innerText;
        getEle("i"+i).value = getEle("i"+last).value; 
    }
    getEle("t"+num).innerText = "";
    getEle("l"+num).innerText = "";
    getEle("i"+num).value = ""; 
    inputArea_evt.removeEventListener("click", handler3, false);
}


var dragged;

/* events fired on the draggable target */
document.addEventListener("drag", function(event) {

}, false);

document.addEventListener("dragstart", function(event) {
  // store a ref. on the dragged elem
  dragged = event.target;
  console.log(dragged.id);
  // make it half transparent
  event.target.style.opacity = .5;
  event.target.style.color = "red";
}, false);

document.addEventListener("dragend", function(event) {
  // reset the transparency
  event.target.style.opacity ="";
  event.target.style.color ="";
}, false);

/* events fired on the drop targets */
document.addEventListener("dragover", function(event) {
  // prevent default to allow drop
  event.preventDefault();
}, false);

document.addEventListener("dragenter", function(event) {
  // highlight potential drop target when the draggable element enters it
  if (event.target.tagName == "TD") {
    event.target.style.border = "2px solid purple";
  }

}, false);

document.addEventListener("dragleave", function(event) {
  // reset background of potential drop target when the draggable element leaves it
  if (event.target.tagName == "TD") {
    event.target.style.border = "";
  }

}, false);

document.addEventListener("drop", function(event) {
  // prevent default action (open as link for some elements)
  event.preventDefault();
  // move dragged elem to the selected drop target
//   if (event.target.className == "dropzone") {
    if (event.target.tagName == "TD") {
        if(!event.target.id){
            event.target.children[0].style.border = "border:0";
            console.log(event.target.children[0].id);
            var id2 = "i" + event.target.children[0].id.replace("it","");
            
        }else{
            event.target.style.border = "";
            console.log(event.target.id);
            if(event.target.id[0] == "l") var id2 = "i" + event.target.id.replace("l","");
            if(event.target.id[0] == "t") var id2 = "i" + event.target.id.replace("t","");
        }
        //若拿空白交換則變成插入
        if(getEle("i" + dragged.id.replace("drag","")).innerText.trim() == "" &&
        getEle("l" + dragged.id.replace("drag","")).innerText.trim() == ""){
            insert_field(true);
            getEle(id2).click();
        }else{
            var id1 = "i" + dragged.id.replace("drag","");
            ex_data(true);
            getEle(id1).click();
            getEle(id2).click();
        }
    }
    //交換 t l i

    //移除
    if(event.target.tagName == "BODY"){
        console.log("sankuchu!");
        console.log(dragged.id.replace("drag",""));
        var id = "i" + dragged.id.replace("drag","");
        delete_field(true);
        getEle(id).click();
    }
//   }
}, false);