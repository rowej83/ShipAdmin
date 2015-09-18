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
<div class="pure-g" style="margin-top:20px;margin-bottom:20px;"><div class="pure-u-1">
<a href="<?php link_to_route('input');?>">Go back</a>
</div></div>
<div class="pure-g" style="margin-top:20px;margin-bottom:20px;"><div class="pure-u-1">
<p style="">**Please note: When pasting results back into excel, right-click and select Paste Special  -> Value or text</p>
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
</html>
