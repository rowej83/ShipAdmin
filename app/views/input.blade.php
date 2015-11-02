<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipment Size Estimator</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>

</head>
<body>

<div style="width:800px;margin:0 auto;">


    @include('main-menu-partial')

@include('sub-menu-ship-estimate')

    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
<div class="pure-u-1"><b style="color:red" id="reloadText"></b></div>
    <div class="pure-u-1">
<h1>Shipment Estimator <small style="font-size: .4em;color:grey;">v1.0</small>
{{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
</h1>
Instructions: Copy a column of CMMFs ( without headings ) from excel into the text field labeled CMMFs. Then, copy the associated quantity column from excel ( without headings ) in to the text field labeled Quantities.

</div>
</div>
@if(isset($response))
<div class="pure-g" style="margin-bottom:15px;"><div class="pure-u-1">

{{$response}}

</div>
</div>
@endif
   <form class="pure-form" action="<?php link_to_route('input'); ?>" method="post">
<div class="pure-g " style="margin-top:30px;"><div class="pure-u-1-3">

    <label for="cmmf" style="display: block;margin-bottom:10px;"><b>CMMFs</b> <small>(Seperated by new line)</small></label>
        <textarea  id="cmmf" placeholder="paste column of cmmfs" type="text" name="cmmf" style="overflow:auto;height:200px"><?php echo Session::get('cmmf'); ?></textarea>



    </div>
    <div class="pure-u-1-3">
      <label style="display: block;margin-bottom:10px;" for="quantity"><b>Quantities</b><small> (Seperated by new line)</small></label>
            <textarea id="quantity" placeholder="paste column of quantities" type="text" name="quantity" style="overflow:auto;height:200px;"><?php echo Session::get('quantity'); ?></textarea>

    </div>
    <div class="pure-u-1-3"></div>
    </div>
{{--<script>     function reset(){
                $('textarea').val('');
                }</script>--}}
    <div class="pure-g" style="margin-top:50px;"><div class="pure-1">   <input type="submit" value="submit shipment" class="pure-button-primary pure-button" style="" class="pure-button"/><input type="button" value="reset" onclick="reset"  id="reset" style="margin-left:10px;" class="pure-button reset"/></div></div>
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
                $("#cmmf").focus();

                });

                    $("#reload").click(function(e){
                       $('#reloadText').text('Loading items from the spreadsheet to the Database. Do not click again. Please wait..');
                  $(this).hide();
                    });


                });
                </script>
</div>
</body>
</html>
