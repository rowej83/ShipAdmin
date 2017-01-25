<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Extract DOs from text</title>
    <meta name="csrf_token" content="{{ csrf_token()}}"/>
    <link rel="stylesheet" href="<?php echo URL::asset('css/styles.css'); ?>">
    <link rel="stylesheet" href="<?php echo URL::asset('css/magnific-popup.css'); ?>">
    <script src="<?php echo URL::asset('js/scripts.js'); ?>"></script>
    <script src="<?php echo URL::asset('js/jquery.magnific-popup.min.js'); ?>"></script>
</head>
<body>

<div style="width:800px;margin:0 auto;">
    @include('main-menu-partial')

    <div class="pure-g" style="margin-top:5px;margin-bottom:10px;">
        <div class="pure-u-1">
            <h1>Extract DOs from text
                {{--<small style="font-size: .4em;color:grey;"> v1.0</small>--}}
                <small style="margin-left:3px; font-size:.4em;color:grey;">by Jason Rowe</small>
            </h1>
            Instructions: Enter text to extract DOs
        </div>
    </div>
    @if(isset($response))
        <div class="pure-g" style="margin-bottom:15px;">
            <div class="pure-u-1">

                {{$response}}

            </div>
        </div>
    @endif
    <form class="pure-form">
        <div class="pure-g" style="margin:30px;">
            <div class="pure-u-1">
                {{ Form::label('extract', 'Enter text to extract DOs', ['class' => 'control-label']) }}<br><br>
                <textarea class="extract" name="extract" id="extract" cols="80" rows="15"></textarea>
            </div>
        </div>

        <div class="pure-g" style="margin-top:50px;">
            <div class="pure-u-1">
                <input type="submit" id="submitButton" value="Descramble" class="pure-button-primary pure-button"
                       style="" class="pure-button"/>
                <input
                        id="reset" type="reset" value="Reset" class="pure-button" style="" class="pure-button"/></div>
        </div>
    </form>
    <div class="pure-g">
        <div class="pure-u-1">

            <div id="resultsbutton">
                <hr style="margin:15px 0 15px 0">
                <input type="button" onclick="selectElementContents(results)"
                       value="Select this to highlight the generated list. Then press Ctrl-C to copy it to the clipboard."/>
            </div>
        </div>
    </div>
    <div class="pure-g">
        <div class="pure-u-1">

            <div id="results">

            </div>
        </div>
    </div>
    <div class="pure-g" style="margin-top:10px;" id="queryresultsbutton">
        <div class="pure-u-1">
            <hr style="margin:15px 0 15px 0">
            <div id="resultCount"></div>
            <div id="resultUniqueCount" style="margin-bottom:10px;"></div>
            <div>

                <input type="button" onclick="selectElementContents(queryresults)"
                       value="Select this to highlight the generated query. Then press Ctrl-C to copy it to the clipboard."/>
            </div>
        </div>
    </div>
    <div class="pure-g">
        <div class="pure-u-1">

            <div id="queryresults" style="word-wrap: break-word;">

            </div>
        </div>
    </div>


    <script src="<?php echo URL::asset('js/do-descrambler.js'); ?>"></script>

</div>
<div id="loader" class="mfp-hide" style="background-color:inherit;color:white;width:300px;margin:0 auto; padding:20px;">
    <p
            style="text-align: center;margin:0 auto;width: 300px;">Loading. Please wait</p>

    <div id="" class="spinner" style="color:white;">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>
</div>
</body>
</html>
