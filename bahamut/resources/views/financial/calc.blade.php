<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/calc.css">
    <link rel="stylesheet" href="css/bluebutton.css">
    <!-- jQuery v1.9.1 -->
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <!-- toastr v2.1.4 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" rel="stylesheet"  />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

    <link rel="icon" href="cash.png" type="image/gif" sizes="16x16">
    <title>記帳</title>
</head>
<body>
<div id="content" style="position:relative;margin-top:0%;left:15%;">
    <div id="left" style="display:inline-block">
        <button id="addField">增加輸入欄位</button> 
        <button id="subField">減少輸入欄位</button> 
        <span id="saveTitle"></span>
        <div><table id="showList"></table></div>
        <hr> 
        <div id="showTotal">&ensp;</div><br>
        <button id="add_d" name="click">餘額加到 Detail</button>
        <button id="cost_d" name="click">添加 Detail 花費項</button>
        <br><br>
        <div id="inputArea"></div>
        <div class="btn_set_1">
            <button id="equal" class="">等於</button>
            <button id="clear" class="">清除</button> 
            <button onclick="show_slide_num2()">重載</button>
        </div>
        <div class="btn_set_2">
            <button onclick="ex_data()">交換</button>
            <button onclick="insert_field()">插入</button>
            <button onclick="delete_field()">移除</button>
        </div>
    </div>

    <table id="right" style="position:absolute;top:0;left:30%;">
        
    </table>
    <table id="right2" style="position:absolute;top:0;left:40%;">
        
    </table>
    <div id="right3" style="position:absolute;top:130px;left:35%;">
        <button id="n_save">存檔</button>
        <input id="slide_save" type="range" min="1" max="24" step="1" value="1" oninput="show_slide_num()" onchange="show_slide_num()">
        <span id="slide_save_value">1</span>
        <br><br><br><br>
        <button id="n_load">讀取</button>
        <input id="slide_load" type="range" min="1" max="24" step="1" value="0" oninput="show_slide_num2()" onchange="show_slide_num2()">
        <span id="slide_load_value">0</span>
        <br><br>
        <button id="ll_btn" class="btn1"><<</button>
        <button id="l_btn" class="btn1">←</button>
        <button id="r_btn" class="btn1">→</button>
        <button id="rr_btn" class="btn1">>></button>
        &emsp;
        <br><br>
        <!--常用區-->
        <button id="pay" name="click" class="btn2">薪水</button>
        <button id="bh" name="click" class="btn2">家用</button>
        <button id="shyDi" name="click" class="btn2">學貸</button>
        <button id="carDi" name="click" class="btn2">車貸</button>
        <br><br>
        <button id="lunch" name="click" class="btn2">中餐</button>

    </div>
 </div>
    <xmp id="list_proto" style="display:none;">
        <tr id="drag*" draggable="true">    
            <td id="l*" name="list">&emsp;</td>
            <td>
                &emsp;&emsp;&emsp;&emsp;<input id="it*" name="inputTitle" type="text" size="15" style="border:0;">
            </td>
            <td id="t*">&emsp;</td>
        </tr>   
    </xmp>   
    <xmp id="input_proto" style="display:none;">
        <input type="text" name="input" id="i*"> 
        <br>
    </xmp>    
    <xmp id="save_proto" style="display:none;">
        <tr>
            <td><button id="save_*">存入*txt*</button></td>
            <td><button id="load_*">載入*txt*</button><br></td>
        </tr>
    </xmp>     
    <script src="../../js/Util.js"></script>
    <script src="../../js/ListenEvent.js"></script>
    <script src="../../js/calc.js"></script> 
    <script src="../../js/toast.js"></script> 
    <!-- https://www.bootcss.com/p/buttons/ -->
</body>
</html>