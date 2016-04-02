<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rest Macys.com DB</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">


    @include('main-menu-partial')
    @include('sub-menu-macys')


    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1"><b style="color:red" id="reloadText"></b></div>
        <div class="pure-u-1">
            <h1>Reset Macys.com DB
                <small style="font-size: .4em;color:grey;">v1.0</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
            <b style="color:red;">Reset all DB Entries?</b><br><br>
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">

                {{$response}}

            </div>
        </div>
    @endif

    <form class="pure-form" action="<?php echo route('deleteMacysDBSubmission'); ?>" method="post">
        <div class="pure-g " style="margin-top:30px;">
            <div class="pure-u-1-3">

                <label for="delete" style="display: block;margin-bottom:10px;">Type Y-E-S below to reset your
                    DB.</label>
                <input type="text" name="delete" id="deleteText">


            </div>

            <div class="pure-u-2-3"></div>
        </div>
        <div class="pure-g " style="margin-top:30px;">
            <div class="pure-u-1-3">


                <input type="checkbox" name="deleteLocal" value="deleteLcal" > Delete local .pdf files

            </div>

            <div class="pure-u-2-3"></div>
        </div>
        {{--<script>     function reset(){
                        $('textarea').val('');
                        }</script>--}}
        <div class="pure-g" style="margin-top:50px;">
            <div class="pure-1"><input type="submit" value="Reset DB" class="pure-button-primary pure-button" style=""
                                       class="pure-button"/><input type="button" value="reset" onclick="reset"
                                                                   id="reset" style="margin-left:10px;"
                                                                   class="pure-button reset"/></div>
        </div>
    </form>


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
                $("#deleteText").val('');
                $("#deleteText").focus();

            });

            $("#reload").click(function (e) {
                $('#reloadText').text('Loading items from the spreadsheet to the Database. Do not click again. Please wait..');
            });


        });
    </script>
</div>
</body>
</html>
