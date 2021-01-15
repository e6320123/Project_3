<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fit</title>
</head>
<body>
    
    身高
    <input id="tall" type="text">
    體重
    <input id="fat" type="text">
    <button onclick="calcBMI()">計算</button>
    BMI
    <input id="bmi" type="text">
    <br>
    <br>
    <div>
        <form action="/" method="get">
            表格
            <input id="row" name="row" type="text">
            x
            <input id="col" name="col" type="text">
            <button type="submit">產生</button> 
        </form>
    </div>
    <div>
        @yield('table')
    </div>
    <div>
        新增食物熱量紐
        <br>
        加入常吃食物進sql 以後直接顯示快捷按鈕
        計算每日紀錄進sql 以後都能撈出做報表
    </div>
</body>
<script src={{ URL::asset('js/Util.js') }}></script>
<script>

    var util = new Util();
    function getEle(ele){
        return util.getSpan(document,ele);
    }

    function calcBMI(){
        let tall = getEle("tall").value | 0;
        let fat = getEle("fat").value | 0;
        if(tall==0||fat==0) return;

        tall = tall/100;
        tall = Math.pow(tall,2);
        let bmi = fat/tall;
        bmi = util.formatFloat(bmi,2);
        console.log(bmi);
        document.getElementById("bmi").value = bmi;
    }
</script>
</html>