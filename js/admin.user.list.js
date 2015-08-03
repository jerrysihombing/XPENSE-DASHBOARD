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
		//"ajax": baseUrl+"exe/load_user.php"
		"ajax": {
			url: baseUrl+"exe/load_user.php?baseUrl="+baseUrl,
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
			{ "searchable": false },
			null,
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" }
		]
	});
	
	oTable.fnFilterOnReturn();
	
	$(".btn-add").click(function() {
		location.href=baseUrl+"admin/user/add";
	});
	
	$(".confirmOk").click(function() {
		var id = $(".modal-body #idToDelete").val();
		location.href=baseUrl+"admin/user/delete/"+id;
	});
	
} );

$(document).on("click", ".deleteTrigger", function () {
    var id = $(this).data('id');
	var userId = $(this).data('user_id');
	$(".modal-body #idToDelete").val(id);
	$(".modal-body #userId").html(userId);
});

$(document).on("click", ".viewTrigger", function () {
	var id = $(this).data('id');
	var dataString = "id=" + id;
	
	$.ajax({
			type: "POST",
			url: baseUrl+"admin/stores/"+id,
			data: dataString,
			beforeSend: function() {
				//showProcessing();
			},
			success: function(data, textStatus, xhr) {
				$(".modal-body #detailStore").html(data);
			},
			error: function(xhr, textStatus, errorThrown) {
			},
			complete: function(xhr, textStatus) {
				//hideProcessing();
			}
		});
});


