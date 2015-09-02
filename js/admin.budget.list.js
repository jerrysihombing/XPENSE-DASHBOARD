/* Table initialisation */
$(document).ready(function() {
	var oTable;
	var uid = $("#uid").val();
	
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
			url: baseUrl+"exe/load_budget.php?baseUrl="+baseUrl+"&uid="+uid,
			/*
			data: function (data) {
				data.someKey =  'velue';
			},
			*/
		},
		"columns": [
			{ "class": "al-right" },
			null,
			null,
			null,
			{ "searchable": false, "class": "al-right" },
			{ "searchable": false },
			{ "searchable": false, "sortable": false, "class": "al-center" },
			{ "searchable": false, "sortable": false, "class": "al-center" }
		],
		"order": [[ 0, "desc" ]]
	});
	
	oTable.fnFilterOnReturn();
	
	$(".btn-add").click(function() {
		location.href=baseUrl+"admin/budget/add";
	});
	
	$(".confirmOk").click(function() {
		var id = $(".modal-body #idToDelete").val();
		$("#deleteConfirm").modal('hide');
		location.href=baseUrl+"admin/budget/delete/"+id;
	});
	
} );

$(document).on("click", ".deleteTrigger", function () {
    var id = $(this).data('id');
	var name= $(this).data('name');
	var year= $(this).data('year');
	var month= readMonth($(this).data('month'));
	var store= $(this).data('store');
	
	$(".modal-body #idToDelete").val(id);
	$(".modal-body #name").html(name);
	$(".modal-body #store").html(store);
	$(".modal-body #year").html(year);
	$(".modal-body #month").html(month);
});


function readMonth(v) {
	var ret = "";
	switch (v) {
		case 1:
			ret = "January";
			break;
		case 2:
			ret = "February";
			break;
		case 3:
			ret = "March";
			break;
		case 4:
			ret = "April";
			break;
		case 5:
			ret = "May";
			break;
		case 6:
			ret = "June";
			break;
		case 7:
			ret = "July";
			break;
		case 8:
			ret = "August";
			break;
		case 9:
			ret = "September";
			break;
		case 10:
			ret = "October";
			break;
		case 11:
			ret = "November";
			break;
		case 12:
			ret = "December";
			break;
	}
	return ret;
}