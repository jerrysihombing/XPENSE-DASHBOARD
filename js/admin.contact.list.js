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
		//"ajax": baseUrl+"exe/load_contact.php"
		"ajax": {
			url: baseUrl+"exe/load_contact.php?baseUrl="+baseUrl,
			/*
			data: function (data) {
				data.someKey =  'velue';
			},
			*/
		},
		"columns": [
			null,
			null,
			null,
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" }
		]
	});
	
	oTable.fnFilterOnReturn();
	
	$(".btn-add").click(function() {
		location.href=baseUrl+"admin/contact/add";
	});
	
	$(".confirmOk").click(function() {
		var id = $(".modal-body #idToDelete").val();
		$("#deleteConfirm").modal('hide');
		location.href=baseUrl+"admin/contact/delete/"+id;
	});
	
} );

$(document).on("click", ".deleteTrigger", function () {
    var id = $(this).data('id');
	var name= $(this).data('name');
	$(".modal-body #idToDelete").val(id);
	$(".modal-body #name").html(name);
});
