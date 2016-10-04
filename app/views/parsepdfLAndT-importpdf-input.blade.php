<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Lord & Taylor Packing Slips to DB</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')
    @include('sub-menu-LAndT')
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Add Lord & Taylor Packing Slips to DB
                <small style="font-size: .4em;color:grey;"> v1.0</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
            Instructions: Upload a Lord & Taylor packing slip to add POs to the DB. Can add multiple pdfs at one time.
            <hr>
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">

                {{$response}}

            </div>
        </div>
    @endif
<div class="pure-g">
    <div class="pure-u-2-5">
        <form class="pure-form" action="parseLAndTPDF" method="post" enctype="multipart/form-data">
            <div class="pure-g" style="margin:30px;">

                {{ Form::file('packinglist[]', ['class' => 'form-control','multiple'=>true]) }}
            </div>
            {{--<script>     function reset(){
                            $('textarea').val('');
                            }</script>--}}
            <div class="pure-g" style="margin-top:50px;">
                <div class="pure-1"><input type="submit" id="submitButton" value="Add POs to DB" class="pure-button-primary pure-button"
                                           style="" class="pure-button"/></div>
            </div>
        </form>
    </div>
    <div class="pure-u-3-5">
        <div style="border-left:solid 1px grey;">
            <div  style="text-align: center; padding-top:20px;color:red;">*When saving pdfs to be imported make sure that they are saved using Chome with the print settings shown below.</div>
            <div ><img style="margin:0 auto;width:318px;display:block;padding-top:20px;" src="{{URL::asset('images/print-settings.png');}}" alt=""></div>
        </div>
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
            $("#submitButton").click(function (e) {

                $.magnificPopup.open({
                    items: {
                        src: '#loader'
                    },
                    type: 'inline',
                    closeOnBgClick:false,
                    enableEscapeKey:false
                });


            });
//            $("input:radio").click(function(e){
//            if($(this).attr("id")=="option-two"){
//                   $('#addShipments').show();
//                }else{
//                    $('#addShipments').hide();
//                }
//
//            });


        });
    </script>
</div>
<div id="loader" class="mfp-hide" style="background-color:inherit;color:white;width:300px;margin:0 auto; padding:20px;"><p
            style="text-align: center;margin:0 auto;width: 300px;">Loading. Please wait</p>

    <div id="" class="spinner" style="color:white;">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>
    <p style="color:white;text-align: center;margin:0 auto;width: 300px;margin-top:10px;">Depending on how many packing lists your uploading, this may take a few minutes.</p>
</div>

</body>
</html>
