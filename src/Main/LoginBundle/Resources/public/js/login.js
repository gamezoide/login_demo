$(document).ready(main);

function main() {
//http://www.jqueryrain.com/2014/09/particleground-jquery-plugin-background-particle-systems/
    validate();
    
   // $('#wrap').particleground();
}

function validate() {
    $('#login').validate({
        highlight: function (element, errorClass, validClass) {
            $(element).parent().addClass('has-error', 1000, "easeOutBounce");
            //$(element).parent().find('.bar').after().addClass('has-error', 1000, "easeOutBounce");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parent().removeClass('has-error', 1000, "easeOutBounce");
            //$(element).parent().find('.bar').after().removeClass('has-error', 1000, "easeOutBounce");
        },
        errorClass: "invalid",
        errorElement: "div",
        rules: {
            'user': {
                required: true
            },
            'pass': {
                required: true
            }
        },
        messages: {
            'user': {
                required: 'Campo requerido'
            },
            'pass': {
                required: 'Campo requerido'
            }
        },
        submitHandler: function (form) {
            //$('#error').remove();
            //$('.loader_min').fadeIn();
            checkCredentials();
        }
    });
}

function checkCredentials() {
    $.ajax({
        type: "post",
        url: $('#login').attr('action'),
        dataType: 'json',
        data: $('#login').serialize(),
        success: function (data) {
            status_find = 0;
            if (!data['status']) {
                //$('.loader_min').hide();
                $('#pass').addClass('error-field');
                $('#user').addClass('error-field');
                //display_error($('.login-group'), data['data']);
            } else {
                //$('.loader_min').hide();
                $(location).attr('href', $('#welcome_path').val());
            }
        },
        error: function (request, status, error) {
            //$('.loader_min').hide();
            //show_error_modal(request.responseText);
            //status_find = 0;
        }
    });
}