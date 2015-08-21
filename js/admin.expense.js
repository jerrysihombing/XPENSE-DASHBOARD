
$(document).ready(function() {
	
	FormValidation.init();
	
	$("#checkAll").click(function() {
		var checked = $(this).prop("checked");
		$(".chkAccount").prop("checked", checked);
	});
	
	$(".btn-back").click(function() {
		location.href=baseUrl+"admin/expense";
	});
	
	$(".alertOk").click(function() {
		$('#inputAlert').modal('hide');
		$('#accountList').collapse('show');
	});
	
});
