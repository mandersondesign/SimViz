<?php
$plotID = $plot->plotID;

if ($plot->plotID == 0) {
  if ($datafile == "") {
    $datafile = 'Front_Left_Wheel_data.json';
  }
}
?>

<script src="http://mdanderson.me/projects/isis/js/highcharts.js" type="text/javascript"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/js/modules/exporting.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/_lib/jquery.cookie.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/_lib/jquery.hotkeys.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/jquery.jstree.js"></script>


<!--SocketIO-->
<script src="http://gamewisp.com:8086/socket.io/socket.io.js"></script>

<div id="extremeLow" style="display:none;">0</div>
<div id="extremeHigh" style="display:none;">1000</div>
<button id="zoomSend" style="display:none;"></button>

<script type="text/javascript">
  $(function () {
    var chart; // global
    var receiver = 0;
    var checked = 0;
    var includeFilePath = "<?= base_url(); ?>include/";

    $(document).ready(function() {

      var chartTitle = 'TestBench <?= $simulation->simTestBenchID; ?> / Plot Version #<?= $plot->plotVersion; ?>';
      
      var options = {
        chart: {
          renderTo: 'container',
          zoomType: 'x',
          resetZoomButton: {
            position: {
              // align: 'right', // by default
              // verticalAlign: 'top', // by default
              x: 0,
              y: -30
            }
          },
          type: 'line',
          marginRight: 130,
          marginBottom: 75,
          events: {
            click : function (event)
            {
              console.log('click');
            },
            selection: function(event) {
              // log the min and max of the primary, datetime x-axis
              if (event.xAxis != null)
              {
                
                var preMin = chart.xAxis[0].min;
                var preMax = chart.xAxis[0].max;
                
                var postMin = event.xAxis[0].min;
                var postMax = event.xAxis[0].max;
                
                console.log(chart.xAxis[0].min);
                console.log(chart.xAxis[0].max);
                console.log(event.xAxis[0].min);
                console.log(event.xAxis[0].max);
                
                
                var maxes = Math.abs(postMax-preMax);
                var mins = Math.abs(postMin-preMin);
                
                console.log(maxes);
                console.log(mins);
                
                var diff = maxes / (mins);
                
                
                console.log("Diff:"+diff);
                
                console.log("Chart Interval Pre: "+chart.xAxis[0].tickInterval);
                
                chart.xAxis[0].tickInterval = chart.xAxis[0].tickInterval / diff;
                chart.redraw();
                
                
                console.log("Chart Interval Post: "+chart.xAxis[0].tickInterval);
                
                console.log(
                  Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].min),
                  Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].max)
                );
                // log the min and max of the y axis
                console.log(event.yAxis[0].min, event.yAxis[0].max);
                
                
                
                $("#extremeLow").html(postMin);
                $("#extremeHigh").html(postMax);
                $("#zoomSend").trigger('click');
                
              }
              else
              {
                console.log("reset?");
                $("#extremeLow").html('0');
                $("#extremeHigh").html('500');
                $("#zoomSend").trigger('click');
              }
            }
          }
        },

        subtitle: {
          text: 'Source: META',
          x: -20
        },
        xAxis: {
          type: 'datetime',
          min: 0,
          tickInterval: 10, // SET THIS
//          tickPixelInterval: 80,
          tickmarkPlacement: 'on'
          ,labels: {
            formatter: function() {
              
              if (this.value < 0)
                this.value = 0;
              
              return this.value + "";
              //return Highcharts.dateFormat('%S', (this.value) * 1000).toUpperCase();
            }
          },
          title: {
            text: 'Milliseconds'
          }
        },
        title: {
          text: chartTitle
        },


        yAxis: {
          title: {
            text: 'Value'
          },
          plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
        },
        tooltip: {
          formatter: function() {
            return '<b>'+ this.series.name +'</b><br/>'+
              Math.round(this.x)  +' msec | '+ this.y +'';
          }
        },
        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'top',
          x: -10,
          y: 100,
          borderWidth: 0
        },
        series: []
        
      };


      chart = new Highcharts.Chart(options);


      $(".yaxisvar").live("click", function()
      {
            
        var data_link = includeFilePath+'data/testbenches/DriveTrain/cfg1/'+$(this).val();
        console.log(data_link);
        var name = $(this).attr("id");
        name = name.substring(1);
        
        console.log("Receiver" +receiver);
        console.log("Checked" +checked);
        
        if (receiver == 1 && checked == 1)
        {
          console.log("check is supposed to be happening...");
          $("#y"+name).attr('checked', true);

        }
        else if (receiver == 1 && checked == 0)
        {
          $(this).prop("checked", false);
        }
        
        if( $(this).is(':checked') ) 
        {
          if (receiver == 0)
          {
            checked = 1;
            console.log("Send Check");
            var message = '{ "ySeriesID" : "'+name+'", "checked" : '+checked+' }';
            console.log("Message: "+message);
            socket.emit('sendseries', message);
          }
          
          doChartYDraw(data_link, name);
          chart.redraw();
          
          receiver = 0;
        }
        else
        {
          console.log("attempt remove");
          
          if (receiver == 0)
          {
            checked = 0;
            console.log("Send UnCheck");
            var message = '{ "ySeriesID" : "'+name+'", "checked" : '+checked+' }';
            console.log("Message: "+message);
            socket.emit('sendseries', message);
          }
          
          name = name.split("__").join(".");
          //remove series from chart
          var series = chart.series;

          for (var i = 0; i < series.length; i++)
          {
            console.log("series name: "+series[i].name);
            if (series[i].name == name)
            {
              chart.series[i].remove(true);
              break;
            }
          }
          
          receiver = 0;
        }
            
            
      });
      
      $(".xaxisvar").live("click", function()
      {
            
        var data_link = includeFilePath+$(this).val();
        console.log(data_link);
        var name = $(this).attr("id");
        name = name.substring(1);
        
        console.log(name);
        
        
        
        if( $(this).is(':checked') ) 
        {
          var categories = [];
          chart.xAxis[0].setCategories(categories);
          
          doChartXDraw(data_link, name);
          chart.redraw();
        }
        else
        {
          
          //remove series from chart
        }
            
            
      });
      
      $("#treeSelectedDataLink").live("click", function()
      {
            
        var data_link = includeFilePath+"data/"+$("#treeSelectedDataLink").html();
        console.log(data_link);
        var name = $("#treeSelectedName").html();
        //name = name.substring(1);
        console.log("Name:" + name);
        console.log("Receiver" +receiver);
        console.log("Checked" +checked);
        
        doChartYDrawTree(data_link, name);
        chart.redraw();

        receiver = 0;
        
//        if (receiver == 1 && checked == 1)
//        {
//          console.log("check is supposed to be happening...");
//          $("#y"+name).attr('checked', true);
//
//        }
//        else if (receiver == 1 && checked == 0)
//        {
//          $(this).prop("checked", false);
//        }
//        
//        if( $(this).is(':checked') ) 
//        {
//          if (receiver == 0)
//          {
//            checked = 1;
//            console.log("Send Check");
//            var message = '{ "ySeriesID" : "'+name+'", "checked" : '+checked+' }';
//            console.log("Message: "+message);
//            socket.emit('sendseries', message);
//          }
//          
//          doChartYDrawTree(data_link, name);
//          chart.redraw();
//          
//          receiver = 0;
//        }
//        else
//        {
//          console.log("attempt remove");
//          
//          if (receiver == 0)
//          {
//            checked = 0;
//            console.log("Send UnCheck");
//            var message = '{ "ySeriesID" : "'+name+'", "checked" : '+checked+' }';
//            console.log("Message: "+message);
//            socket.emit('sendseries', message);
//          }
//          
//          //name = name.split("__").join(".");
//          //remove series from chart
//          var series = chart.series;
//
//          for (var i = 0; i < series.length; i++)
//          {
//            console.log("series name: "+series[i].name);
//            if (series[i].name == name)
//            {
//              chart.series[i].remove(true);
//              break;
//            }
//          }
//          
//          receiver = 0;
//        }
            
            
      });
      

<?php
if ($plotID == 0) {
  ?>
          $.getJSON('<?= base_url() . "include/data/" . $datafile; ?>', function(json) {


            var series = {
              id: 'series',
              name: 'RC Car Data',
              data: []
            }

            var categories = [];
            $.each(json.Data, function (i, obj) {
              //alert(obj);
              console.log(obj.Name+", "+obj.Value);
              series.data.push(obj.Value);
              categories.push(obj.Name);
            });

            //options.series = series;

            //alert(options.series.data);
            //chart = new Highcharts.Chart(options);
            chart.xAxis[0].setCategories(categories);
            chart.addSeries(series);
            //alert(chart.series[0].data);
            //chart = new Highcharts.Chart(options);
            chart.xAxis[0].setExtremes(6, 10);
            chart.showResetZoom();

          }).error(function(request, err) 
          {
            console.log('error');
            console.log(err);
          });
  <?php
} else if ($plotID != 0) {
  ?>
        
          var settings = <?= $plot->plotSettings; ?>;
          console.log(settings);
          $("#plotSettings").html(settings);
          var jsonLink = '<?= base_url(); ?>include/<?= $plot->plotJSON; ?>';    
          var setName = settings.series.name;
          var finName = setName.split(".").join("__");
             
          doChartYDraw('<?= base_url(); ?>include/'+settings.series.data_link, finName);
          $('#y'+finName).prop("checked", true);
              
//          setName = settings.xaxis;
//          finName = setName.split(".").join("__");
          //doChartXDraw('<?= base_url(); ?>include/'+settings.series.data_link, finName);
          chart.redraw();
              
          $('#x'+finName).prop("checked", true);
              
          //chart.xAxis[0].setExtremes(0, 50);
          chart.showResetZoom();
                          
  <?php
}
?>
       
      function doChartYDraw(jsonFilePath, seriesName)
      {
        seriesName = seriesName.split("__").join(".");
        $.getJSON(jsonFilePath, function(json) {
              
          console.log("getJson");
              
          var categories = [];
                
          var pointRatio = $("#pointRatio").html();      
              
              
          var time = new Array();
          
          $.each(json.variables, function (i, obj) {
            if (obj.name == "time")
            {
              $.each(obj.data, function (j, obj2) {
                time.push(obj2);
              });
            }
          });      
                
           console.log(time);
           
           console.log('Looking for SeriesName'+seriesName);
           
          $.each(json.variables, function (i, obj) {
            
            console.log(obj.name);
            
            if (obj.name == seriesName)
            {
              
              
              var series = {
                data: []
              }

              var pCounter = 0;
              $.each(obj.data, function (j, obj2) {
                
                var arr=new Array();
                
                if (pCounter%pointRatio == 0)
                {
                  arr[0]=time[j] * 100;
                  arr[1]=obj2;
                  series.data.push(arr);
                }
                pCounter++;
              });
                      
              series.name = obj.name;
              series.pointInterval = 3600*1000;
              series.pointStart = 0;
              chart.addSeries(series);
                      
              //chart.xAxis[0].setCategories(categories);
            }
          });
          
          chart.redraw();
        })
        .error(function(request, err) 
        {
          console.log('error');
          console.log(err);
        });
      }
      
      function doChartYDrawTree(jsonFilePath, seriesName)
      {
        
        $.getJSON(jsonFilePath, function(json) {
              
          console.log("getJson");
              
          var categories = [];
                
          var pointRatio = $("#pointRatio").html();      
              
              
          var time = new Array();
          
          $.each(json.variables, function (i, obj) {
            if (obj.name == "time")
            {
              $.each(obj.data, function (j, obj2) {
                time.push(obj2);
              });
            }
          });      
                
                
          $.each(json.variables, function (i, obj) {
            if (obj.name == seriesName)
            {
              var series = {
                data: []
              }

              var pCounter = 0;
              $.each(obj.data, function (j, obj2) {
                
                var arr=new Array();
                
                if (pCounter%pointRatio == 0)
                {
                  arr[0]=time[j] * 100;
                  arr[1]=obj2;
                  series.data.push(arr);
                }
                pCounter++;
              });
                      
              series.name = obj.name;
              series.pointInterval = 3600*1000;
              series.pointStart = 0;
              chart.addSeries(series);
                      
              //chart.xAxis[0].setCategories(categories);
            }
          });
          
          chart.redraw();
        })
        .error(function(request, err) 
        {
          console.log('error');
          console.log(err);
        });
      }
      
      function doGetTimeArray(jsonFilePath)
      {
        var time = new Array();
        $.getJSON(jsonFilePath, function(json) {
               
          
                
          
        }); 
        
        return time;
      }
      
      
      function doChartXDraw(jsonFilePath, name)
      {
        
        name = name.split("__").join(".");
        $.getJSON(jsonFilePath, function(json) {
              
          var categories = [];
                
          $.each(json.variables, function (i, obj) {
            if (obj.name == name)
            {
              var series = {
                data: []
              }

              var pCounter = 0;
              
              $.each(obj.data, function (j, obj2) {
                if (pCounter%25 == 0)
                  categories.push(obj2);
                
                pCounter++;
              });
                      
              //              series.name = obj.name;
              //              chart.addSeries(series);
                      
              chart.xAxis[0].setCategories(categories);
            }
          });
                
          
        }); 
      }
      
      
      function old()
      {
               
          
                  
        $.getJSON(jsonLink, function(json) {
                  
          //alert('json');
                  
                    

          var categories = [];
          console.log('cat defined');
          $.each(json.variables, function (i, obj) {
            //console.log('json');
                      
            var series = {
              data: []
            }
                      
                      
                      
            console.log('Count: '+i);
            $.each(obj.data, function (j, obj2) {
              //console.log('data');
              if (obj.name=="time")
              {
                console.log("Time: "+obj2);
                categories.push(obj2);
              }
              else
              {
                series.data.push(obj2);
                console.log(obj2);
              }
              //alert(obj);
              //console.log(obj.Name+", "+obj.Value);  
            });

            //options.series = series;

            //alert(options.series.data);
                      
                      
            if (obj.name!="time")
            {
              series.name = obj.name;
              chart.addSeries(series);
            }
            else
            {
              chart.xAxis[0].setCategories(categories);
            }
                      
          });
          //alert(chart.series[0].data);
          //chart = new Highcharts.Chart(options);
          //chart.xAxis[0].setExtremes(0, 100);
          // custom zoom reset button (is used instead of the default one)
          var myZoomButton = chart.renderer.button('Reset zoom',chart.chartWidth-150,25,function(){
            chart.xAxis[0].setExtremes( null, null, false );
            chart.redraw();
            myZoomButton.hide();
          });

          myZoomButton.hide().add();
          myZoomButton.show();
          chart.redraw();
        }); 
      }
      
      //$("table").tablesorter();
      
      vTable = $('.vTable').dataTable({
        "bJQueryUI": false,
        "bAutoWidth": false,
        "sDom": '<"H"fl>t<"F"ip>'
      });


      //===== Dynamic table toolbars =====//		

      $('#dyna .tOptions').click(function () {
        $('#dyna .tablePars').slideToggle(200);
      });	

      $('.tOptions').click(function () {
        $(this).toggleClass("act");
      });
    });
  



// Socket IO Stuff! 
	var socket = io.connect('http://gamewisp.com:8086');

	// on connection to server, ask for user's name with an anonymous callback
	socket.on('connect', function(){
		// call the server-side function 'adduser' and send one parameter (value of prompt)
		
	});

  socket.on('updatechart', function (data) {
		//console.log(data);
    var json = $.parseJSON(data);
    
    console.log("receive Message to change zoome");
    chart.xAxis[0].setExtremes(json.low, json.high);
    chart.showResetZoom();
    
	});
  
  socket.on('updatecheck', function (data) {
		//console.log(data);
    var json = $.parseJSON(data);
    
    console.log("receive Message to check: "+"#y"+json.ySeriesID);
    receiver = 1;
   
    checked = json.checked;
    console.log($("#y"+json.ySeriesID).val());
    
    $("#y"+json.ySeriesID).trigger('click');
    
    if (json.checked == 1)
      $("#y"+json.ySeriesID).attr('checked', true);
    else
      $("#y"+json.ySeriesID).attr('checked', false);
    
	});
  

	// on load of page
	$(function(){
    
    $('#zoomSend').click( function() {
      
      console.log("high"+$('#extremeHigh').html());
      console.log("high"+$('#extremeHigh').val());
      console.log("high"+$('#extremeHigh').text());
      
			var message = '{ "low" : ' +$('#extremeLow').html()+', "high" : '+$('#extremeHigh').html()+'}';
      
      
			//$('#data').val('');
			// tell server to execute 'sendchat' and send along one parameter
			socket.emit('sendzoom', message);
		});

	});
  
  
  function thingy()
  {
    
  }




});




</script>

<div class="fluid">
  <div class="widget grid9">
    <div class="whead"><h6>Plot</h6><div class="clear"></div></div>
    <div class="body">
      <div id="container" style=""></div>

    </div>
  </div>

  <div class="grid3">
    <div class="widget">
      <div class="whead"><h6>Options</h6><div class="clear"></div></div>
      <div style="margin: 5px 5px 15px 15px;">
        <a id="AddVariableDialog_open" href="#_" class="buttonS bBlue" style="margin: 10px 0 0 0;">
          Add Variables
        </a><br/>
        <a id="AddVariableDialog_open" href="#_" class="buttonS bRed" style="margin: 10px 0 0 0px;">
          Clear Variables
        </a>
        <br/>
        <a id="AddVariableDialog_open" href="#_" class="buttonS bGreen" style="margin: 10px 0 0 0;">
          Save Plot
        </a>
        <br/>
        <a id="AddVariableDialog_open" href="#_" class="buttonS bGreyish" style="margin: 10px 0 0 0px;">
          Save as...
        </a>
        <br/>
        
        <div id="plotSettings"></div>
      </div>
    </div>
    <div class="widget">
    <div class="whead"><h6>Versions</h6><div class="clear"></div></div>
      <div id="dyna" class="hiddenpars">
        <a class="tOptions" title="Options"><img src="<?= base_url(); ?>include/images/icons/options" alt="" /></a>
        <table cellpadding="0" cellspacing="0" border="0" class="vTable" id="dynamicVTable">
          <thead>
            <tr>
              <th>Version<span class="sorting" style="display: block;"></span></th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>
            <?php
  //          print_r($plots);
            foreach ($plots as $plt) {
              ?>
              <tr class="gradeX">
                <td><a href="http://mdanderson.me/projects/isis/plotutil/graph/plot/<?= $testBenchID; ?>/<?= $plt->plotID; ?>">Version <?= $plt->plotVersion; ?></a></td>
                <td><?= date("m/d/Y h:M:s", strtotime($plt->plotCreateDate)); ?></td>
              </tr>
            <?php
          }
          ?>
          </tbody>
        </table> 
      </div>
      <div class="clear"></div> 
    </div>
  </div>
</div>
<div class="fluid">
  <div class="widget grid8">
    <div class="whead"><h6>Y-Axis</h6><div class="clear"></div></div>
    <div class="body">
      <ul id="yaxislist">
        <?php
        


        //for all elements in "Features", print the name and fill out form
        foreach ($jsonDecoded["nodes"] as $var) {
          //Name value of the feature
          $name = $var["name"];

          
          $nameAdj = str_replace(".", "__", $name);

          $data_link = $var["data_link"];
          $children = $var["children"];

//            if ($data_link == null && $children != null)
//            {
//              foreach ($children as $child)
//              {
//                $name2 = $child["name"];
//                $data_link2 = $child["data_link"];
//                $children2 = $child["children"];
//              }
//            }
          ?>
          <li>
            <input type="checkbox" name="elements[]" id="y<?= $nameAdj; ?>" value="<?= $data_link; ?>" class="yaxisvar" />
            <label for="y<?= $nameAdj; ?>" class="eltLabel"><?= $name; ?></label>
          </li>
          <?php
          
        }
        ?>	
      </ul>
      
<!--      <div id="demo2" style="height:100px;">
        
      </div>-->
      <script type="text/javascript">
        //Setup The JSTree
        $(function () {

        

//          function traverse(name, jsonObj) {
//            if( typeof jsonObj == "object" && jsonObj != null) {
//              
//              var finalTreeJSON = "";
//              
//              $.each(jsonObj, function(k,v) {
//                  // k is either an array index or object key
//                  
//                  name = name + '.' + v.name;
//                  
//                  if (v.data_link != null)
//                  {
//                    //console.log(name)
//                    //console.log(v.data_link);
//                    
//                    finalTreeJSON += '{ "data" : "'+v.name+'", "metadata" : { "id" : 23, "data_link" : "'+v.data_link+'" },';
//                    finalTreeJSON += '"children" : []';
//                    finalTreeJSON += '},';
//                    
//                    //return;
//                  }
//                  else
//                  {
//                    finalTreeJSON += '{ "data" : "'+v.name+'", "metadata" : { id : 23 },';
//                    finalTreeJSON += '"children" : [' + traverse(name, v.children) + ']';
//                    finalTreeJSON += '},';
//                  }
//                  
//                  //traverse(name, v.children);
//              });
//              
//              finalTreeJSON = finalTreeJSON.substring(0, finalTreeJSON.length-1);
//              
//              return finalTreeJSON;
//            }
//            else 
//            {
//                // jsonOb is a number or string
//            }
//          }
//        
//          var treeJson = "";
////          $.getJSON('$jsonTreeFilePathTest;?>', function(json) {
////            treeJson = json;
////          });
////        
//          treeJson = $.parseJSON('=$jsonPlainTest;?>');
//           
//        //alert(treeJson.nodes[0].name);
//          
//          
//          var finalTreeJSON = '{ "data" : [';
//          
//          
//          $.each(treeJson, function(i, obj)
//          {
//            //console.log(obj.name);
//            
//            finalTreeJSON += '{ "data" : "'+obj.name+'", "metadata" : { id : 23 },';
//            
//            finalTreeJSON += '"children" : [' + traverse(obj.name, obj.children) + ']';
//            
//            finalTreeJSON += '},';
//          });
//          
//          finalTreeJSON = finalTreeJSON.substring(0, finalTreeJSON.length-1);
//          
//          finalTreeJSON += "]}";
          
          //document.write(finalTreeJSON);
          
//          $("#demo2").jstree({
//            "json_data" : finalTreeJSON
//            ,
//            "plugins" : [ "themes", "json_data" ]
//          });

          $("#demo2").jstree({
            "plugins" : ["themes", "json_data", "ui"],
            "json_data" : {
              "ajax" : {

                // the URL to fetch the data
                "url" : "<?=base_url();?>graph/getTreeJSON"

              }
            }
          }).bind("select_node.jstree", function (event, data) {
              var selectedObj = data.rslt.obj;
              //alert(selectedObj.attr("id") + ' '+ selectedObj.attr("name") + ' '+ selectedObj.attr("data_link"));
              
              $("#treeSelectedDataLink").html(''+selectedObj.attr("data_link"));
              $("#treeSelectedName").html(''+selectedObj.attr("name"));
              
              $("#treeSelectedDataLink").trigger("click");
              
          });

//          var text = "";
//          $.post("<?=base_url();?>graph/getTopTreeJSON", {}, function(result){
//            text = result;
//            console.log(text);
//            
//          });

          
        });
      </script>
      
      
      
    </div>
  </div>

  <div class="widget grid4">
    <div class="whead"><h6>X-Axis</h6><div class="clear"></div></div>
    <div class="body">
      <ul>
        <?php
        //for all elements in "Features", print the name and fill out form
        foreach ($jsonDecoded["nodes"] as $var) {
          //Name value of the feature
          $name = $var["name"];

          if ($name == "Time")
          {
          $nameAdj = str_replace(".", "__", $name);
          $data_link = $var["data_link"];
          $children = $var["children"];

//            if ($data_link == null && $children != null)
//            {
//              foreach ($children as $child)
//              {
//                $name2 = $child["name"];
//                $data_link2 = $child["data_link"];
//                $children2 = $child["children"];
//              }
//            }
          ?>
          <li>
            <input type="radio" name="xaxisoption" id="x<?= $nameAdj; ?>" value="<?= $data_link; ?>" class="xaxisvar" <? if (strtolower($name) == "time") echo 'checked'; ?>/>
            <label for="x<?= $nameAdj; ?>" class="eltLabel"><?= $name; ?></label>
          </li>
          <?php
          }
        }
        ?>	
      </ul>
      
      
    </div>
  </div>
  
  
  <!--Data-->
  <div id="pointRatio" style="display:none;">10</div>
  <div id="treeSelectedDataLink" style="display:inline;"></div>
  <div id="treeSelectedName" style="display:inline;"></div>
  
</div>


<script type="text/javascript">
  
$(document).ready(function() {
  
  var temp = <?= $plot->plotSettings; ?>;
  $("#plotSettings").html(''+temp);
  console.log($("#plotSettings").html());
  
  var variables = "";
  
  getVarList();
  
  $('#AddVariableDialog').dialog({
    autoOpen: false,
    width: 1000,
    height: 600,
    buttons: {
      "Close": function () {
        $(this).dialog("close");
      }
    }
  });
  
  
  $('#AddVariableDialog_open').live("click", function () {
    //var sID = $(this).attr("id");
    //console.log(sID.substr(1, sID.length-1));
    //$("#siSaveID").val(sID.substr(1, sID.length-1));
    
    $('#AddVariableDialog').dialog('open');
    return false;
  });
  
  
  function getVarList()
  {
    $.getJSON("<?php echo base_url(); ?>graph/getListJSON/",
      {},
      function(json)
      {
        variables = json;
      });
  }
  
  $("#find").live('click',function(e)
  {
    e.preventDefault();
    var variablesearch = $.trim($("#variableSerach").val());
    //var userID = $("#userID").val();
    
    var post_data = {
      variablesearch : variablesearch,
      variables : variables
    };
    
    $.post('<?=base_url();?>graph/getSearchResults', post_data, function(result)
    {
      $("#searchResults").html(result);
    })
    
    
//    var codes = "";
//
//    var count = 0;
//    var currentLetter = "A";
//
//    var searchArray = variablesearch.split(" ");
//
//    $.each(variables, function(i, obj)
//    {
//      var isFound = 0;
//      
//      $.each(searchArray, function(j, obj2)
//      {
//        var isFoundNew = obj.name.search(new RegExp(obj2, "i"));
//        
//        if (isFoundNew > 0)
//          isFound++;
//      });
//      
//      
//      
//      console.log(isFound);
//      if (isFound >0 && isFound == searchArray.length)
//      {
//        codes += '<div id="'+obj.name+'" class="fluid varbox';
//
//        if (count == 0)
//          codes += ' first'; //add first
//
//        codes += '" style="margin:5px 5px 5px 0px; padding:0 0px 0 5px; border-bottom:1px #ccc solid;" data_link="data/'+obj.data_link+'">';
//        codes += '<div class="grid12"  class="" style="font-size: 11px;">';
//        codes += ''+ obj.name + '';
//
//        codes += '<div class="clear"></div></div></div>';
//
//        count++;
//      }
//    });

//    if (count == 0)
//    {
//      codes += '<div id="0" class="fluid contactbox" style="margin:5px 0 5px 0; border:1px #ccc solid;">';
//      codes += '<div class="grid12" class="gameInfo">';
//      codes += '<h6>No Matching Variables Found</h6>';
//      codes += '<div class="clear"></div></div></div>';
//    }

    //console.log(codes);

    //$("#searchResults").html(codes);
    //console.log($("#searchResults").html());

  });
  
  $(".varbox").live("click", function(e)
  {
    var name = $(this).attr("id");
    var datalink = $(this).attr("data_link");
    
    $(this).css('background-color', '#ccc');
    
    
    var nameAdj = name.replace(".", "__");
    var newCheckBox = "";
    
    newCheckBox += '<li>';
    newCheckBox += '<input type="checkbox" name="elements[]" id="y'+nameAdj+'" value="'+datalink+'" class="yaxisvar" />';
    newCheckBox += '<label for="y'+nameAdj+'" class="eltLabel">'+name+'</label>';
    newCheckBox += '</li>';
    
    $("#yaxislist").append(newCheckBox);
  });
  
});
</script>

<div id="AddVariableDialog" class="dialog" title="Add Variable to Plot">
  <h3>Search for Variables</h3>
  
  <div class="fluid">
    <div class="widget grid12">
      <div class="searchLine" style="margin-top:0px;">
        <form action="">
          <button type="submit" name="find" id="find" value=""><span class="icos-search"></span></button>
          <input type="text" name="search" id="variableSerach" class="" placeholder="Enter search text..." />
        </form>
      </div>
      <div id="searchResults" style="margin: 3px 0px 3px 3px;" class="scrollpane">

      </div>
    </div>
  </div>
</div>







