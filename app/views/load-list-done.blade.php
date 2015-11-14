<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Estimator</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Shipment Estimator
                <small style="font-size: .4em;color:grey;"> v1.0</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
            <?php  echo 'Loading of DB complete.<br><br>';

            //  echo $itemCount.' items are currently in the DB'.'<br><br>';

            ?>
            <a href="<?php echo route('estimateHome'); ?>" class="pure-button">Go back</a>

        </div>
    </div>


    {{--<script>$("form").keypress(function(ev){--}}
    {{--if (ev.keyCode == 13) {--}}
    {{--$("form")[0].submit();--}}
    {{--}--}}
    {{--});</script>--}}
    <script>
        $(document).ready(function () {
            //doesn't wait for images, style sheets etc..
            //is called after the DOM has been initialized
//                    alert("hello");
            $(".reset").click(function (e) {
                e.preventDefault();
                $("textarea").val('');
                $("#items").focus();


            });


        });
    </script>
</div>
</body>
</html>


