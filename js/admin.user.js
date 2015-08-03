
$(document).ready(function() {
	
	FormValidation.init();
	
	$("#checkAll").click(function() {
		var checked = $(this).prop("checked");
		$(".chkStore").prop("checked", checked);
	});
	
	$(".btn-back").click(function() {
		location.href=baseUrl+"admin/user";
	});
	
	$(".alertOk").click(function() {
		$('#inputAlert').modal('hide');
		$('#storeList').collapse('show');
	});
	
});
