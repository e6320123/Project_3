@extends('mb.main')


@section('xmp_smallFrame')
    @foreach ($gameAry as $gameInfo)   
    <table class="smallFrame">
        <tr>
            <td>
                <img id="{{$gameInfo->inx}}" class="img_{{$gameInfo->imgsize}}" src="img/{{$gameInfo->imgsrc}}" alt="">
            </td>
            <td>
                <span>{{$gameInfo->platform}}</span>&ensp;{{$gameInfo->name}}
            </td>
        </tr>
    </table>   
    @endforeach
 @endsection
 @section('xmp_linkTable')
 <table id="linkTable" class="news" style="width: 100%;">
    <tr>
        <td colspan="4">
            <p style="color:rgb(121, 98, 73);
            border-bottom:2px solid gray ;
            font-size:20px; "> 最新消息 </p>
        </td>
    </tr>
    <tr>
        <td>
            <a href="link/ps4.html"><img style="width: 100%;" src="img/ps4/1.jpg" alt=""></a></td>
        <td>
            <a href="link/xb.html"><img style="width: 100%;" src="img/xb/xb1.jpg " alt=""></a></td>
        <td>
            <a href="link/pirate.html"><img style="width: 100%;" src=" img/pirate/pirate1.jpg" alt=""></a>
        </td>
        <td>
            <a href="link/gta5.html"><img style="width: 100%;" src="img/gta5/gta5.jpg " alt=""></a>
        </td>
    </tr>
    <tr>
        <td>
            <p id="ps4" style="font-size: 15px;"><a href="link/ps4.html"><span style="color: grey;">《魔物獵人
                        世界：Iceborne》將推出
                        PS4 主機上蓋、控制器及穿戴式揚聲器等相關產品</span></a></p>
        </td>
        <td>
            <p style="font-size: 15px;"><a href="link/xb.html"><span style="color: grey;">《戰爭機器 5》將推出特別款式
                        Xbox One X 主機同捆組 伴隨凱特揭露以血束縛的真相 </span></a></p>
        </td>
        <td>
            <p style="font-size: 15px;"><a href="link/pirate.html"><span
                        style="color: grey;">由迪士尼正版授權，西山居遊戲推出的《神鬼奇航 榮耀之海 》（iOS / Android）日前於 ChinaJoy 2019
                        中亮相。 </span></a></p>
        </td>
        <td>
            <p style="font-size: 15px;"><a href="link/gta5.html"><span style="color: grey;"> 《俠盜獵車手
                        5》線上模式「鑽石賭場度假村」發放開幕參與者獎勵 </span></a></p>
        </td>
    </tr>
</table>
<br> 
 @endsection
 
 @section('xmp_linkTable2')
 @foreach ($newsAry as $newsInfo)   
 <div style="border-bottom:3px dotted gray ; padding-bottom: 30px;">
    <table id="linkTable2">
        <tr>
            <td>
                <a href="/{{$newsInfo->fname}}">
                    <img style="height: 150px;width:150px;" src="img/{{$newsInfo->fname}}/{{$newsInfo->imgName}}.jpg" alt="">
                </a>
            </td>
            <td>
                <h5 style="color:rgb(255, 115, 0);">
                    <a href="/{{$newsInfo->fname}}">{{$newsInfo->title}}</a>
                </h5>
                <p style="color:rgb(115, 126, 126);">{{$newsInfo->content}}</p>
            </td>
        </tr>
    </table>
</div>
<br>
@endforeach
@endsection 
 @section('xmp_linkTable3')
 <div style="background-color: rgb(254, 255, 246);overflow: hidden;width:10px;height:30px;">
    <a href=""><img style="display:none; " id="r" src=" " alt=""></a>
<div style="display:none; " class="tuku_up">
    <a href=""><img class="smallScreen" src=" " alt=""></a>
</div>
<a href=""><img style="display:none; " id="l" src=" " alt=""></a>
<div style="display:none; " id="downini" class="tuku_down">
    <a href=""><img class="star" src=" " alt=""></a>
    <a href=""><img class="end" src=" " alt=""></a>
</div>
<a href=""><img style="display:none; " id="r" src=" " alt=""></a>
 @endsection    