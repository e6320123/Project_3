<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body> 
    @if ($chg_game)
    <table border=1>
        <tr>
            <td>inx</td>
            <td>platform</td>
            <td>name</td>
            <td>imgsrc</td>
            <td>imgsize</td> 
        </tr>
        @foreach ($gameAry as $aGame) 
        <tr>
            <td>{{$aGame->inx}}</td>
            <td>{{$aGame->platform}}</td>
            <td>{{$aGame->name}}</td>
            <td>{{$aGame->imgsrc}}</td>
            <td>{{$aGame->imgsize}}</td> 
        </tr>
        @endforeach 
    </table>  
    @else
    <table border=1>
        <tr>
            <td>id</td>
            <td>fname</td>
            <td>imgName</td>
            <td>title</td>
            <td>content</td> 
        </tr>
        @foreach ($newsAry as $aNews) 
        <tr>
            <td>{{$aNews->id}}</td>
            <td>{{$aNews->fname}}</td>
            <td>{{$aNews->imgName}}</td>
            <td>{{$aNews->title}}</td>
            <td>{{$aNews->content}}</td> 
        </tr>
        @endforeach 
    </table> 
    @endif
</body>
</html>