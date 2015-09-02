
$(document).ready(function() {
	
	$("#budget").autoNumeric('init');
	
	FormValidation.init();
	
	$(".btn-back").click(function() {
		location.href=baseUrl+"admin/budget";
	});
	
});
