
(function ($) {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit', function () {
        var check = true;

        for (var i = 0; i < input.length; i++) {
            if (validate(input[i]) == false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });

    function validate(input) {
        if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        } else {
            if ($(input).val().trim() == '') {
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }


    $("#comment").on({
        "focus": function (event) {
            $(event.target).select();
        },
        "keyup": function (event) {
            $(event.target).val(function (index, value ) {
                return value.replace(/\D/g, "")
            });
        }
    });
    $('#montobs').mask("#.##0,00", {reverse: true});

//

/*// set content on click
    $('.button').click(function(e) {
        e.preventDefault();
        setContent($(this));
    });

// set content on load
    $('.button.active').length && setContent($('.button.active'));

    function setContent($el) {
        $('.button').removeClass('active');
        $('.bt').hide();

        $el.addClass('active');
        $($el.data('rel')).show();
    }*/




})  (jQuery);
$(document).ready(function() {
    $('#btn1').on('click', function(){
        $.ajax({
            type: "GET",
            url: "monedalocal",
            success: function(response) {
                $('#div-results').html(response);
            }
        });
    });

    $('#btn2').on('click', function(){
        $.ajax({
            type: "GET",
            url: "monedain",
            success: function(response) {
                $('#div-results').html(response);
            }
        });
    });
});

$('.drop-down-show-hide').hide();

$('#dropDown').change(function () {
    $('.drop-down-show-hide').hide()
    $('#' + this.value).show();

});

/*=======================================================================================================================*/

$(document).ready(function(){

   /* $(".hide").on('click', function() {
        var id = $(this).attr('id');
        var n = id.replace("hide",'');
        $("#element"+n).hide();
        console.log("#element"+n);
        console.log(n);
        return false;
    });*/

    $(".show").on('click', function() {
        var id = $(this).attr('id');
        var n = id.replace("show",'');
        console.log(n);
        $("#element"+n).show();
        $("#calc_fecha").show();
        $("#element1"+n).hide();
        $("#deudat").hide();
        console.log("#element"+n);
        console.log("#element1"+n);
        return false;
    });
    $(".ocultar").on('click', function() {
        var id = $(this).attr('id');
        var n = id.replace("ocultar",'');
        $("#element"+n).hide();
        $("#element1"+n).show();
        $("#deudat").show();
        $("#calc_fecha").hide();
        return false;
    });
});