var FormValidation = function () {

    var handleValidation1 = function() {
        // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form1 = $('#form_data');
            var error1 = $('.alert-danger', form1);
            var success1 = $('.alert-success', form1);

            form1.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    passwd: {
                        minlength: 5,
                        required: true
                    },
                    newPasswd: {
                        minlength: 5,
                        required: true
                    },
                    retypeNewPasswd: {
                        minlength: 5,
                        required: true,
                        equalTo: "#newPasswd"
                    }
                },
                messages: {
                        passwd: {
                            required: "Please enter current password",
                            minlength: "Your current password must be at least 5 characters long"
                        },
                        newPasswd: {
                            required: "Please provide a new password",
                            minlength: "Your new password must be at least 5 characters long"
                        },
                        retypeNewPasswd: {
                            required: "Please re-type new password",
                            minlength: "Your new password must be at least 5 characters long",
                            equalTo: "Please enter the same password as new password"
                        }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    //success1.hide();
                    //error1.show();
                    $("#alertMessage").html("You have some form errors. Please check below.");
                    success1.addClass("hide");
                    error1.removeClass("hide");
                    FormValidation.scrollTo(error1, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element).closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element).closest('.form-group').removeClass('has-success').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label.closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                },

                submitHandler: function (form) {
                    //success1.show();
                    //error1.hide();
                    //success1.removeClass("hide");
                    
                        var passwd = $("#passwd").val();
                        var newPasswd = $("#newPasswd").val();
                        
                        var dataString = "passwd=" + passwd + "&newPasswd=" + newPasswd;
                        
                        $.ajax({
                            type: "POST",
                            url: baseUrl+"chpass/commit",
                            data: dataString,
                            beforeSend: function() {
                                success1.addClass("hide");
                                error1.addClass("hide");
                                $("#imgLoading").removeClass("hide");
                            },
                            success: function(data) {
                                if (data.substr(0,5) != "Error") {
                                    $(".alert-success").removeClass("hide");
                                    FormValidation.scrollTo(error1, -200);
                                }
                                else {
                                    $("#alertMessage").html(data);
                                    $(".alert-danger").removeClass("hide");
                                    FormValidation.scrollTo(error1, -200);
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                $("#imgLoading").addClass("hide");
                                $("#alertMessage").html(textStatus + ": " + errorThrown);
                                $(".alert-danger").removeClass("hide");
                                FormValidation.scrollTo(error1, -200);
                            },
                            complete: function(xhr, textStatus) {
                                $("#imgLoading").addClass("hide");
                            }
                        });
                    
                }
                
            });
            
    }

    return {
        //main function to initiate the module
        init: function () {

            handleValidation1();

        },

	// wrapper function to scroll to an element
        scrollTo: function (el, offeset) {
            pos = el ? el.offset().top : 0;
            jQuery('html,body').animate({
                scrollTop: pos + (offeset ? offeset : 0)
            }, 'slow');
        }

    };

}();