<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Size Estimator</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>


</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')


    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">Total # of POs: {{$totalOfPOs}}</div>
        <div class="pure-u-1">
            <h3 style="margin-bottom:10px;">Generated Query:</h3>

            <input type="button" onclick="selectElementContents(queryString)"
                   value="Select this to highlight the generated query. Then press Ctrl-C to copy it to the clipboard."/>
            @if(isset($queryString))
                <div class="pure-u-1" style="margin-top:10px;"><p id="queryString"
                                                                  style="word-wrap: break-word;">{{$queryString}}</p>
                </div>
            @endif
        </div>
    </div>
    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            <h3 style="margin-bottom:10px;">Generated PO List for SAP:</h3>

            <input type="button" onclick="selectElementContents(POs)"
                   value="Select this to highlight the generated query. Then press Ctrl-C to copy it to the clipboard."/>
            @if(isset($POs))
                <div class="pure-u-1" style="margin-top:10px;" id="POs">


                    @if(!empty($POs))
                        {{$POs}}
                    @endif

                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
