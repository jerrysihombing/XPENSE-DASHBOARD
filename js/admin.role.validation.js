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
                    roleName: {
                        minlength: 3,
                        required: true
                    }
                },
                messages: {
                        roleName: {
                            required: "Please enter Role Name",
                            minlength: "Role Name must consist of at least 3 characters"
                        }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    //success1.hide();
                    //error1.show();
                    $("#alertMessageForm").html("You have some form errors. Please check below.");
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
	
                        $('.chkMenu').each(function() {
                            if ($(this).prop('checked')) {
                                sel = true;
                                return false; // break loop
                            }
                            return true;
                        });
                        
                        return sel;  
                    }();
                    
                    if (ret) {
                        
                        var todo = $("#todo").val();
                        
                        if (todo == "add") {
                                
                                var roleName = $("#roleName").val();
                                var dataString = "roleName=" + roleName;
                                
                                $.ajax({
                                    type: "POST",
                                    url: baseUrl+"admin/isRoleExist",
                                    data: dataString,
                                    beforeSend: function() {
                                        success1.addClass("hide");
                                        error1.addClass("hide");
                                        $("#imgLoading").removeClass("hide");
                                    },
                                    success: function(data) {
                                        if (data == "not exist") {
                                                error1.addClass('hide');
                                                form.submit();
                                        }
                                        else {
                                            $("#alertMessageForm").html(data);
                                            $(".alert-danger").removeClass("hide");
                                            FormValidation.scrollTo(error1, -200);
                                        }
                                    },
                                    error: function(xhr, textStatus, errorThrown) {
                                        $("#imgLoading").addClass("hide");
                                        $("#alertMessageForm").html(textStatus + ": " + errorThrown);
                                        $(".alert-danger").removeClass("hide");
                                        FormValidation.scrollTo(error1, -200);
                                    },
                                    complete: function(xhr, textStatus) {
                                        $("#imgLoading").addClass("hide");
                                    }
                                });
                        
                        }
                        else {
                                error1.addClass('hide');
                                form.submit();
                        }
                        
                        //error1.addClass('hide');
                        //form.submit();
                    }
                    else {
                        $("#alertMessage").html("Please select at least one menu.");
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