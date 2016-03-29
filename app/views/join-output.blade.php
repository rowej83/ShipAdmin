<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Size Estimator</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>


</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')
    <div class="pure-g" style="margin-top:20px;">
        <div class="pure-g" style="margin-top:20px;margin-bottom:5px;">
            <div class="pure-u-1">
                <a class="pure-button" href="<?php link_to_route('joinHome');?>">Go back with Input saved to
                    re-Query</a>
            </div>
            <div class="pure-u-1">
                <h1>Advanced Query Builder
                    <small style="font-size: .4em;color:grey;"> v1.2</small>
                    {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
                </h1>

            </div>
            <div class="pure-u-1">
                <p style="">Copy and Paste the below query into Advanced Query in Viaware to bring up only the order
                    numbers or shipments you want.</p>
            </div>
        </div>
    </div>


    <div class="pure-g" style="">
        <div class="pure-u-1">


            @if(isset($itemCount) && isset($uniqueItemCount))
                <div class="pure-u-1" style="margin-top:10px;"><p id="count" style="word-wrap: break-word;">Total # of
                        items: {{$itemCount}}</p>
                <p>Total # of unique items: {{$uniqueItemCount}}</p></div>
            @endif
        </div>
    </div>

    <div class="pure-g" style="margin-top:20px;">
        <div class="pure-u-1">
            <h3 style="margin-bottom:10px;">Generated Query:</h3>

            <input type="button" onclick="selectElementContents(result)"
                   value="Select this to highlight the generated query. Then press Ctrl-C to copy it to the clipboard."/>
            @if(isset($response))
                <div class="pure-u-1" style="margin-top:10px;"><p id="result"
                                                                  style="word-wrap: break-word;">{{$response}}</p></div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
