<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parse Chargeback Spreadsheet</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('DataTables/datatables.min.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
    <script src="<?php echo URL::asset('DataTables/jquery.dataTables.js'); ?>"></script>



</head>
<body>


<div style="width:1350px;margin:0 auto;">
    @include('main-menu-partial')
    <div class="pure-g" style="margin-top:50px;">
        <div class="pure-u-1">
            <h1>Parse Chargeback Spreadsheet</h1>
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">
                <input type="button" onclick="selectElementContents(table)"
                       value="Select this to copy ( then press Ctrl-C )"/>
                {{$response}}

            </div>
        </div>
    @endif


</div>
</body>
</html>
