
<p class="alert alert-info">
	This widget is fetching data from an external source with the use of code <code>data-widget-load="<i class="text-primary">[data source]</i>"</code>. Turn on auto refresh by adding the timer like so <code>data-widget-refresh="15"</code> , which will refresh the widget and fetch the data every 15 seconds.
</p>
<!-- dummy file -->
<div class="row">
	<div class="col-sm-7">
		<code>data-widget-load="<i class="text-primary">ajax/demowidget.php</i>"</code>
	</div>
	<div class="col-sm-5 text-right">
		<div class="jarviswidget-timestamp no-margin"></div>
	</div>
</div>

<div id="flotcontainer" style="height:200px; width:100%;"></div>


	<script type="text/javascript">

		/*
		* RUN PAGE GRAPHS
		*/
	
		// Load FLOAT dependencies (related to page)
		loadScript("js/plugin/flot/jquery.flot.cust.js", loadFlotResize);
	
		function loadFlotResize() {
			loadScript("js/plugin/flot/jquery.flot.resize.js", loadFlotToolTip);
		}
	
		function loadFlotToolTip() {
			loadScript("js/plugin/flot/jquery.flot.tooltip.js", generateRandomFlot);
		}

		function generateRandomFlot(){

		    function GenerateSeries(added){
		        var data = [];
		        var start = 100 + added;
		        var end = 500 + added;
		 
		        for(i=1;i<=20;i++){        
		            var d = Math.floor(Math.random() * (end - start + 1) + start);        
		            data.push([i, d]);
		            start++;
		            end++;
		        }
		 
		        return data;
		    }
		 
		    var data1 = GenerateSeries(0);
		    var data2 = GenerateSeries(10);    
		 
		    var markings = [
		        { xaxis: { from: 1, to: 2 }, color: "#E8E8E8" },
		        { xaxis: { from: 4, to: 5 }, color: "#E8E8E8" },
		        { xaxis: { from: 7, to: 8 }, color: "#E8E8E8" },
		        { xaxis: { from: 10, to: 11 }, color: "#E8E8E8" },
		        { xaxis: { from: 13, to: 14 }, color: "#E8E8E8" },
		        { xaxis: { from: 16, to: 17 }, color: "#E8E8E8" },
		        { xaxis: { from: 19, to: 20 }, color: "#E8E8E8" }
		 
		    ];
		 
		    var options = {            
		             series: {
		                lines: { show: true, lineWidth: 3 },
		                shadowSize: 0
		            },
		            grid: {
		                markings: markings,
		                backgroundColor: { colors: ["#D1D1D1", "#7A7A7A"] }
		            }      
		    };
		 
		    $.plot($("#flotcontainer"),
		        [
		            {data:data1},
		            {data:data2}
		        ], options
		    );

		}



	</script>