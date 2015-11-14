<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieve packinglists from Kohls.com POs</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>


</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')


    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(isset($response))
                <p>{{$response}}</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>
