<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retrieve packinglists from Lord & Taylor POs</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>


</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')
     @include('sub-menu-LAndT')


    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(isset($response))
                <p>{{$response}}</p>
            @endif
        </div>
    </div>
    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(isset($outputpath))
                <a href="{{$outputpath}}" target="_blank">Click to download your generated Packing Slip</a>
            @endif
        </div>
    </div>
    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(!empty($nonGroundPos))
                <p>Please note - the following POs are NOT shipping via Ground</p>
                @foreach($nonGroundPos as $po)
                    {{$po.'<br>'}}
                @endforeach
            @endif
        </div>
    </div>
</div>
</body>
</html>
