<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Size Estimator</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>

<script type="text/javascript">
    function selectElementContents(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
        }
    }

</script>
</head>
<body>
<div style="width:960px;margin:0 auto;">
    @include('main-menu-partial')


<div class="pure-g" style="margin-top:20px;margin-bottom:20px;"><div class="pure-u-1">
            Links: <br><br>
            <a href="<?php echo route('input');?>">Go back with input saved to re-Query</a><br><br>
            <a id="reload" href="<?php echo route('silentReload');?>">Go back with input saved to re-Query AND reload spreadsheet with any new added items.</a>
        </div></div>
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1"><b style="color:red" id="reloadText"></b></div>
<div class="pure-g" style="margin-top:20px;margin-bottom:20px;"><div class="pure-u-1">

</div></div>
@if(isset($missingitems) && !empty($missingitems))
<div class="pure-g" >
<div class="pure-u-1">
{{--<input type="button" onclick="selectElementContents(stats)" value="Select this table to copy (Ctrl-C)"/>--}}
<div style="border:1px solid red;margin-bottom:30px;padding:25px;">
<h2><small style="color:red;">Caution - Item(s) not found in the DB, listed below. Please add and re-run query.</small></h2>
<input type="button" onclick="selectElementContents(missingitems)" value="Select this to copy ( then press Ctrl-C )"/>
<ul  style="list-style-type: none;" id="missingitems">
@foreach($missingitems as $missingitem)
<li>{{$missingitem}}</li>
@endforeach
</ul >
</div>
</div>
</div>
@endif
@if(isset($stats))
<div class="pure-g">
<div class="pure-u-1">
<h3 style="margin-bottom:10px;">Shipment Summary:</h3>
<input type="button" onclick="selectElementContents(stats)" value="Select this table to copy ( then press Ctrl-C )"/>

{{$stats}}

</div>
</div>
@endif

<div  class="pure-g" style="margin-top:50px;">
<div class="pure-u-1">
<h3 style="margin-bottom:10px;">Line-item Detail:</h3>

<input type="button" onclick="selectElementContents(table)" value="Select this table to copy ( then press Ctrl-C )"/>
@if(isset($response))
{{$response}}
@endif
</div>
</div>
</div>
</body>

<script>
    $(document).ready(function() {
        //doesn't wait for images, style sheets etc..
        //is called after the DOM has been initialized
//                    alert("hello");

        $("#reload").click(function(e){
            $('#reloadText').text('Loading items from the spreadsheet to the Database. Do not click again. Please wait..');
        });


    });
</script>
</html>
