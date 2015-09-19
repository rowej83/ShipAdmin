<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Query Builder</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Advanced Query Builder<small style="font-size: .4em;color:grey;"> v1.0</small>
                {{--<small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>--}}
            </h1>
            Instructions: Paste a list of items that will be joined with ' ' and ,'s to be pasted into an advanced query in Viaware.

        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;"><div class="pure-u-1" >

                {{$response}}

            </div>
        </div>
    @endif
    <form class="pure-form" action="<?php link_to_route('join'); ?>" method="post">
        <div class="pure-g " style="margin-top:30px;">
            <div class="pure-u-1-3">
                <b style="margin-top:8px;display:block;">Build Query using:</b>
                <label for="option-one" class="pure-radio">
                    <input id="option-one" type="radio" name="optionsRadios" value="ordernumbers" checked>
                   Outbound Order Numbers
                </label>

                <label for="option-two" class="pure-radio">
                    <input id="option-two" type="radio" name="optionsRadios" value="shipmentnumbers">
                    Shipment Numbers
                </label>
                <input type="checkbox" name="addShipments" id="addShipments" value="addShipments"> Add "SHP" to items<br>
                <label for="option-three" class="pure-radio">
                    <input id="option-three" type="radio" name="optionsRadios" value="commas">
                    Join with commas and no space
                </label>
                <label for="option-four" class="pure-radio">
                    <input id="option-four" type="radio" name="optionsRadios" value="none">
                    None ( Only Join the list together for AQB )
                </label>
            </div>
            <div class="pure-u-1-3">

                <label for="items" style="display: block;margin-bottom:10px;"><b>Items to be joined</b> <small>(Seperated by new line)</small></label>
                <textarea  id="items" placeholder="paste column of items to be joined" type="text" name="items" style="overflow:auto;height:200px"><?php echo Session::get('items'); ?></textarea>



            </div>

            <div class="pure-u-1-3"></div>
        </div>
        {{--<script>     function reset(){
                        $('textarea').val('');
                        }</script>--}}
        <div class="pure-g" style="margin-top:50px;"><div class="pure-1">   <input type="submit" value="Build Query" class="pure-button-primary pure-button" style="" class="pure-button"/><input type="button" value="Reset" onclick="reset"  id="reset" style="margin-left:10px;" class="pure-button reset"/></div></div>
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






        });
    </script>
</div>
</body>
</html>
