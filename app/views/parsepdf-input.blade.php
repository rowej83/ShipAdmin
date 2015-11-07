<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parse Kohls Packing Slips</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Kohls Packing Slip Parser<small style="font-size: .4em;color:grey;"> v1.0</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
Instructions: Upload a Kohls.com packing slip and it will return POs
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;"><div class="pure-u-1" >

                {{$response}}

            </div>
        </div>
    @endif
    <form class="pure-form" action="parsePostPDF" method="post" enctype="multipart/form-data">
        <div class="pure-g" style="margin:30px;">

            {{ Form::file('packinglist', ['class' => 'form-control']) }}
        </div>
        {{--<script>     function reset(){
                        $('textarea').val('');
                        }</script>--}}
        <div class="pure-g" style="margin-top:50px;"><div class="pure-1">   <input type="submit" value="Parse Packing Slip" class="pure-button-primary pure-button" style="" class="pure-button"/></div></div>
    </form>


    {{--<script>$("form").keypress(function(ev){--}}
    {{--if (ev.keyCode == 13) {--}}
    {{--$("form")[0].submit();--}}
    {{--}--}}
    {{--});</script>--}}
    <script>
        $(document).ready(function() {
            //doesn't wait for images, style sheets etc..
            //is called after the DOM has been initialized
//                    alert("hello");
            $(".reset").click(function(e){
                e.preventDefault();
                $("textarea").val('');
                $("#items").focus();


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
</body>
</html>
