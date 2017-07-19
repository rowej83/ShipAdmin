<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Kohls Packing Slips to DB</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')
    @include('sub-menu-kohls')
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Add Kohls.com Packing Slips to DB
                <small style="font-size: .4em;color:grey;"> v1.1</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
            Instructions: Upload a Kohls.com packing slip(s) to add POs to the DB. Can add multiple pdfs at one time.
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">

                {{$response}}

            </div>
        </div>
    @endif
    <form class="pure-form" action="parseKohlsPDF" method="post" enctype="multipart/form-data">
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
    <hr>

    <div class="pure-g" style="margin-top:20px;">

        <div class="pure-u-1">
            <button id="errorareatoggle" style="">Having trouble importing packinglists?</button>
           <div id="errorarea" style="display:none;">
            <p>If you have an error while trying to import a pdf, the file may be corrupt. You can try going to the below website and converting it to another pdf version.</p>
            <br>
            <a href="https://docupub.com/pdfconvert/" target="_blank">Convert PDF</a>
            <p>Select version 1.3, convert and download and try again to import.</p>
            <br>
            <img src="<?php echo URL::asset('images/conversion-example.png') ?>" alt="">
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
           // $('#errorarea').hide();
            $('#errorareatoggle').click(
                function(e){
                    e.preventDefault();
                    $('#errorarea').toggle();

                }
            )
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
