/* Table initialisation */
$(document).ready(function() {
	var oTable;
	
	oTable = $('#list').dataTable({
		"sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
		"sPaginationType": "bootstrap",
		"oLanguage": {
			"sLengthMenu": "_MENU_ records per page"
		},
		"serverSide": true,
		//"processing": true,
		//"ajax": baseUrl+"exe/load_role.php"
		"ajax": {
			url: baseUrl+"exe/load_adm_expense.php?baseUrl="+baseUrl,
			/*
			data: function (data) {
				data.someKey =  'velue';
			},
			*/
		},
		"columns": [
			null,
			null,
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" }
		]
	});
	
	oTable.fnFilterOnReturn();
	
	$(".btn-add").click(function() {
		location.href=baseUrl+"admin/expense/add";
	});
	
	$(".confirmOk").click(function() {
		var id = $(".modal-body #idToDelete").val();
		$("#deleteConfirm").modal('hide');
		location.href=baseUrl+"admin/expense/delete/"+id;
	});
	
} );

$(document).on("click", ".deleteTrigger", function () {
    var id = $(this).data('id');
	var expenseName= $(this).data('expense_name');
	$(".modal-body #idToDelete").val(id);
	$(".modal-body #expenseName").html(expenseName);
});

$(document).on("click", ".viewTrigger", function () {
	var id = $(this).data('id');
	var dataString = "id=" + id;
	
	$.ajax({
			type: "POST",
			url: baseUrl+"admin/accounts/"+id,
			data: dataString,
			beforeSend: function() {
				//showProcessing();
			},
			success: function(data, textStatus, xhr) {
				$(".modal-body #detailAccount").html(data);
			},
			error: function(xhr, textStatus, errorThrown) {
			},
			complete: function(xhr, textStatus) {
				//hideProcessing();
			}
		});

});