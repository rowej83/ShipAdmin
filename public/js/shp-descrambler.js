
//adds getUnique and replaceAt to array prototype for needed functionality.
Array.prototype.getUnique = function () {
    var u = {}, a = [];
    for (var i = 0, l = this.length; i < l; ++i) {
        if (u.hasOwnProperty(this[i])) {
            continue;
        }
        a.push(this[i]);
        u[this[i]] = 1;
    }
    return a;
};
String.prototype.replaceAt = function (index, character) {
    return this.substr(0, index) + character + this.substr(index + character.length);
};
$(document).ready(function () {

    $("#extract").focus();
    $('#queryresultsbutton').hide();
    $('#resultsbutton').hide();
    $("#submitButton").prop('disabled', true);

    $("#extract").bind('input propertychange', function () {
        if (!$.trim($("#extract").val())) {

            $("#submitButton").prop('disabled', true);
        } else {

            $("#submitButton").prop('disabled', false);
        }

    });

    $("#reset").click(function (e) {
        e.preventDefault();
        $("textarea").val('');
        $('#results').empty();
        $('#queryresults').empty();
        $('#resultsbutton').hide();
        $('#queryresultsbutton').hide();
        $("#submitButton").prop('disabled', true);
        $("#extract").focus();


    });

    $("#submitButton").click(function (e) {
        e.preventDefault();
        var text = $("#extract").val();
        var splittext;
        // var numberOnlySelector = /[^0-9]/g;
         var numberOnlySelector = /(SHP)+\d{7}/g;
        for (var i = 0, len = text.length; i < len; i++) {

            if (numberOnlySelector.test(text[i])) {

                text = text.replaceAt(i, " ");

            }


        }

        splittext = text.split(" ");

        var tempArray = [];
        var selector = /(SHP)+\d{7}/;
        splittext.forEach(function (item) {
            if ($.trim(item).length > 0) {

                if (selector.test(item)) {
                    //is a valid DO
                    var tempItem = item.match(/(SHP)+\d{7}/g);
                    //    console.log(tempItem[0]);
                    tempArray.push(tempItem[0]);

                } else {
                    //not valid DO
                }

            } else {

            }

        });


        tempArray = tempArray.getUnique();
        if (tempArray.length > 0) {
            var returnResult = '<ul style="list-style-type: none;">';

            for (i = 0; i < tempArray.length; i++) {

                returnResult += '<li>' + tempArray[i] + '</li>';
            }
            returnResult += '</ul>';
            var ajaxArray = JSON.stringify(tempArray);

            var queryStringResult = '';
            $.ajax({
                type: 'POST',
                url: 'ajaxJoinSHP',
                data: {'shps': ajaxArray},
                dataType: 'json',
                cache: false,
                beforeSend: function (xhr) {

                    var token = $('meta[name="csrf_token"]').attr('content');;
                    if (token) {
                        return xhr.setRequestHeader('X-CSRF-TOKEN', token);
                    }
                },
                success: function (data) {
                    // do something with ajax data
                    //console.log($('#queryresults').text());
                    // console.log(data.resultQueryString);
                    $('#queryresults').text(data.resultQueryString);
                    //   $('#resultCount').text('Order Count: '+ data.count);
                    $('#resultUniqueCount').text('Unique Order Count: ' + data.unique);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('error...', xhr);
                    //error logging
                },
                complete: function () {
                    //afer ajax call is completed
                }
            });


            $('#resultsbutton').slideDown();
            $('#queryresultsbutton').slideDown();
            $('#results').html(returnResult);


            $("html, body").animate({scrollTop: $(document).height()}, "slow");

        } else {

            $('#results').html('<b style="display:block;margin-top:5px;color:red">No valid DOs found.</b>');
        }


    })

});
