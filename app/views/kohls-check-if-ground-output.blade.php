<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check for non-ground from Kohls.com POs</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>


</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')
    @include('sub-menu-kohls')


    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(isset($response))
                <p>{{$response}}</p>
            @endif
        </div>
    </div>
    @if(!empty($notInDBPos))
        <div class="pure-g" style="margin-top:50px;">
            <div class="pure-u-1">

                <p style="color:red;">Please note - the following POs were not found in the DB.</p>
                @foreach($notInDBPos as $po)
                    {{$po.'<br>'}}
                @endforeach

            </div>
        </div>
    @endif
    @if(!empty($nonGroundPos))
        <div class="pure-g" style="margin-top:50px;">
            <div class="pure-u-1">

                <p style="color:red;">Please note - the following POs are NOT shipping via Ground</p>
                @foreach($nonGroundPos as $po)
                    {{$po.'<br>'}}
                @endforeach

            </div>
        </div>
    @endif
    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            @if(!empty($nonGroundPos))
                @if(!empty($queryPOString))
                    <input type="button" onclick="selectElementContents(queryResult)"
                           value="Select this to highlight the generated query. Then press Ctrl-C to copy it to the clipboard."/>
                    <p id="queryResult">{{$queryPOString}}</p>
                @endif
            @endif
        </div>
    </div>


</div>
</body>
</html>
