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
                    userId: {
                        minlength: 3,
                        required: true
                    },
                    passwd: {
                        minlength: 5,
                        required: true
                    },
                    retypePasswd: {
                        minlength: 5,
                        required: true,
                        equalTo: "#passwd"
                    }
                    /*
                    ,email: {
                        required: true,
                        email: true
                    }
                    */
                },
                messages: {
                        userId: {
                            required: "Please enter User ID",
                            minlength: "User ID must consist of at least 3 characters"
                        },
                        passwd: {
                            required: "Please provide a password",
                            minlength: "Your password must be at least 5 characters long"
                        },
                        retypePasswd: {
                            required: "Please re-type password",
                            minlength: "Your password must be at least 5 characters long",
                            equalTo: "Please enter the same password as above"
                        },
                        email: "Please enter a valid email address"
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    //success1.hide();
                    //error1.show();
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
                    
                    var ret = function() {
                        var sel = false;
	
                        $('.chkStore').each(function() {
                            if ($(this).prop('checked')) {
                                sel = true;
                                return false; // break loop
                            }
                            return true;
                        });
                        
                        return sel;  
                    }();
                    
                    if (ret) {
                        error1.addClass('hide');
                        form.submit();
                    }
                    else {
                        $("#alertMessage").html("Please select at least one store.");
                        $('#inputAlert').modal('show');
                    }
                    
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