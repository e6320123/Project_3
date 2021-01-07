<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="js/Util.js"></script>
    <script src="js/ListenEvent.js"></script>
    <title>Game</title>
    <link rel="icon" href="img/d_icon.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- <link href="bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="c.css">
</head>

<body id="body_bg" class="cntbgcolor">
    <div id="toTop">
        <img id="toTopimg" src="img/up.png" alt="">
    </div>
    <div id="top">
        <div class="mynav">
            <ul class="ul">
                <li>首頁</li>
                <li>線上遊戲</li>
                <li>PC GAME</li>
                <li>PS4</li>
                <li>NS</li>
            </ul>
        </div>
        <div id="search">
            <input type="text" placeholder="請輸入搜尋" name="" id="search_column">
            <img src="img/sear.jpg" alt="">
        </div>
    </div>
    <div id="content" class="cntbgcolor">
        <div id="L_content" class="cntbgcolor">
            @yield('xmp_smallFrame')<!--L content -->
        </div>
        <div id="R_content" class="cntbgcolor">
            @yield('xmp_linkTable')<!--R content -->
            @yield('xmp_linkTable2')<!--R content -->
            @yield('xmp_linkTable3')<!--R content -->
        </div>
    </div> 
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <!-- <script src="jquery.js"></script>
      <script src="bootstrap.min.js"></script>
     -->
    <script src="js/control.js"></script>
    <script src="js/linkPage.js"></script>
    <script src="js/url.js"></script>
</body>

</html>