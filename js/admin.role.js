
$(document).ready(function() {
	
	FormValidation.init();
	
	$("#checkAll").click(function() {
		var checked = $(this).prop("checked");
		$(".chkMenu").prop("checked", checked);
	});
	
	$(".btn-back").click(function() {
		location.href=baseUrl+"admin/role";
	});
	
	$(".alertOk").click(function() {
		$('#inputAlert').modal('hide');
		$('#menuList').collapse('show');
	});
	
});
