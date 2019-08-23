<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Advanced Query Builder</title>

    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <link rel="stylesheet" href="{{ asset('css/tooltipster.css'); }}">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
    <script src="{{asset('js/jquery.tooltipster.min.js');}}"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')
    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Advanced Query Builder
                {{--<small style="font-size: .4em;color:grey;"> v1.2</small>--}}
                <small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>
            </h1>
            Instructions: Paste a list of items that will be joined with ' ' and ,'s to be pasted into an advanced query
            in Viaware.<br><br> Useful for bringing up specific orders in Viaware via Advanced Query by either the order # ( DO ) , SHP #, or Purchase order.<br><br> Please Note - any blank lines submitted will not be included in your result.

        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">

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
                <label for="option-five" class="pure-radio">
                    <input id="option-five" type="radio" name="optionsRadios" value="pos">
                    Purchase Order Numbers
                </label>
                <label for="option-eight" class="pure-radio">
                    <input id="option-eight" type="radio" name="optionsRadios" value="partinventoryscreen">
                    Parts (Inventory Screen)
                </label>
                <label for="option-seven" class="pure-radio">
                    <input id="option-seven" type="radio" name="optionsRadios" value="partoutboundscreen">
                    Parts (Outbound Screen)
                </label>
                <label for="option-six" class="pure-radio">
                    <input id="option-six" type="radio" name="optionsRadios" value="customers">
                    Customers
                </label>

                <label for="option-four" class="pure-radio">
                    <input id="option-four" type="radio" name="optionsRadios" value="none">
                    None ( Join for AQB only ) <img src="{{asset('images/help-button.png')}}" id="joinonly-help" alt="">
                </label>
                <label for="option-three" class="pure-radio">
                    <input id="option-three" type="radio" name="optionsRadios" value="commas">
                    Join with commas and no space <img src="{{asset('images/help-button.png')}}" alt="" id="comma-help" class="">
                </label>
                <label for="option-eleven" class="pure-radio">
                    <input id="option-eleven" type="radio" name="optionsRadios" value="spaces">
                    Join with spaces in between <img src="{{asset('images/help-button.png')}}" alt="" id="spaces-help" class="">
                </label>
                <label for="option-five" class="pure-radio">
                    <input id="option-five" type="radio" name="optionsRadios" value="amazonpos">
                    Amazon POs
                </label>
                <label for="option-nine" class="pure-radio">
                    <input id="option-nine" type="radio" name="optionsRadios" value="docfetcher">
                    DocFetcher
                </label>
                <label for="option-ten" class="pure-radio">
                    <input id="option-ten" type="radio" name="optionsRadios" value="ten-digit">
                    Walmart POs check if 10 digits <img src="{{asset('images/help-button.png')}}" alt="" id="ten-digit-help" class="">
                </label>
                <hr>
                <b>Options:</b>
                <span id="addShipments" style="margin-top:20px;display: block;"> <input type="checkbox" style=""
                                                                                        name="addShipments" id=""
                                                                                        value="addShipments"> Add "SHP" to items <img src="{{asset('images/help-button.png')}}" alt="" id="addshp-help"><br></span>
            </div>
            <div class="pure-u-1-3" style="padding-left:20px;">

                <label for="items" style="display: block;margin-bottom:10px;"><b>Items to be joined</b>
                    <small>(Seperated by new line)</small>
                </label>
                <textarea id="items" placeholder="paste column of items to be joined" type="text" name="items"
                          style="overflow:auto;height:200px"><?php echo Session::get('items'); ?></textarea>


            </div>

            <div class="pure-u-1-3"></div>
        </div>
        {{--<script>     function reset(){
                        $('textarea').val('');
                        }</script>--}}
        <div class="pure-g" style="margin-top:50px;">
            <div class="pure-1"><input type="submit" value="Build Query" class="pure-button-primary pure-button"
                                       style="" class="pure-button"/><input type="button" value="Reset" onclick="reset"
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
                $("textarea").val('');
                $("#items").focus();


            });

            $('#joinonly-help').tooltipster({
                content: $('<div>Useful for creating an IN operator for multiple specific items (cmmfs, brands, store numbers etc..) in advanced query.<br><br></div><div>Given the following input: <br>2100064646<br>2100072125</div><div><br>It will return:</div><div>(\'2100064646\',\'2100072125\')</div><div><br>This can be used in Viaware\'s Advanced Query such as <br><br> od_f.sku in (\'2100064646\',\'2100072125\')  <br><br>This will bring up all orders for those two parts only.</div>')
            });
            $('#comma-help').tooltipster({
                content: $('<div>Useful for including needed shipments when creating Mbols or getting packing lists from Wayfairs site<br><br></div><div>Given the following input: <br>SHP3211111<br>SHP3211112</div><div><br>It will return:</div><div>SHP3211111,SHP3211112</div><div><br>This can be used in Viaware\'s Paperwork screen to generate Mbols. Can also be used to list POs that are needed to be printed from Wayfairs site. </div>')
            });
            $('#ten-digit-help').tooltipster({
                content: $('<div>Used for Walmart POs. They have to be 10 digits long. This will add leading zeros if it isn\'t 10 digits long.</div>')
            });
            $('#addshp-help').tooltipster({
                content: $('<div>When querying by shipment numbers ( SHP ), this option will add the SHP to each item in the list if needed<br><br></div><div>Given the following input: <br>3211111<br>3211112</div><div><br>It will add SHP along with your query such as :</div><div>om_f.shipment in (\'SHP3211111\',\'SHP3211112\')</div><div><br><br>Please note that the Build Query using Shipment Numbers still has to be selected to get this result.</div>')
            });
            $('#spaces-help').tooltipster({
                content: $('<div>Given the following input: <br>3211111<br>3211112</div><div><br>It will produce: <br>3211111 3211112</div>')
            });


        });
    </script>
</div>
</body>
</html>
