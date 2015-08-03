var chart;
 
$(function () {
    var dateObj = new Date();
    dateObj.setMonth(dateObj.getMonth() - 2); // 2 months back
    var currYear = dateObj.getFullYear();
    var currMonth = dateObj.getMonth();
    
    var actYear = $('#actYear').val();
    var actMonth = $('#actMonth').val();
    var storeCode = $('#storeCode').val();
    var clusterCode = $('#clusterCode').val();
    var regionalCode = $('#regionalCode').val();
    
    actYear = (actYear != '' ? actYear : currYear);
    actMonth = (actMonth != '' ? actMonth : currMonth);
    storeCode = (storeCode != '' ? storeCode : 0);
    
    var options = {
        chart: {
            type: 'column',
            renderTo: 'theChart',
            height: 660
        },
        title: {
            text: 'Expense Report'
        },
        subtitle: {
            text: 'Source: ORAFIN'
        },
        xAxis: {
            categories: [],
            //crosshair: true
        },
        yAxis: {
            //min: 0, // allow negative value
            //max: 4,
            title: {
                text: 'Percentage (%)'
            }
        },
        credits: {
            enabled: false
        },
        tooltip: {
            //valueSuffix: '%',
            formatter: function() { 
                return '' + this.x + '<br><span style="color: ' + this.point.series.color + '">\u25CF</span> ' + this.series.name + ': <b>' + this.point.y + '% (Rp ' + this.point.amount + ')</b>';
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            //x: -40,
            //y: 100,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true,
            reversed: false,
            title: {
                text:''
            },
        },
        series: []
    };
    
    /* original code */
    /*
    $.getJSON(baseUrl+'exe/load_expense.php?actYear='+actYear+'&actMonth='+actMonth+'&storeCode='+storeCode, function(json) {
        options.xAxis.categories = json['expense_name'];
        options.series[0] = json['expense_pc'];
        
        options.title.text = 'Expense Report';
        options.subtitle.text = 'Source: ORAFIN';
        options.legend.title.text = json['data_label'];
        
        chart = new Highcharts.Chart(
            options
        );
        
    });
    */
    
    /* trying to follow CI rule */
    $.getJSON(baseUrl+'dboard/getdataytd/'+storeCode, function(json) {
        var seriesCount = json['series_count'];
        if (seriesCount > 0) {
            options.xAxis.categories = json['expense_holder'][0]['name'];
            
            for (var i = 0; i < json['expense_holder'].length; i++) {
                options.series[i] = json['expense_holder'][i]['pc'];
            }
        }
        
        options.title.text = 'Expense Report YTD';
        //options.subtitle.text = 'Source: ORAFIN';
        options.subtitle.text = json['group_name'];
        options.legend.title.text = json['data_label'];
        
        chart = new Highcharts.Chart(
            options
        );
    });    
    
    /*
    $('#theChart').highcharts(
        options
    );
    */
});
