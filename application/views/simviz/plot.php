<?php
$plotID = $plot->PlotID;
?>

<script src="http://mdanderson.me/projects/isis/js/highcharts.js" type="text/javascript"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/js/modules/exporting.src.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/_lib/jquery.cookie.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/_lib/jquery.hotkeys.js"></script>
<script type="text/javascript" src="http://mdanderson.me/projects/isis/plotutil/include/js/plugins/jstree/jquery.jstree.js"></script>


<!--SocketIO-->
<!--<script src="http://gamewisp.com:8086/socket.io/socket.io.js"></script>-->

<div id="extremeLow" style="display:none;">0</div>
<div id="extremeHigh" style="display:none;">1000</div>
<button id="zoomSend" style="display:none;"></button>

<script type="text/javascript">
  $(function () {
    var chart; // global
    var receiver = 0;
    var checked = 0;
    var includeFilePath = "<?php echo base_url(); ?>include/";

    $(document).ready(function() {

      var chartTitle = 'TestBench <?php echo $simulation->simTestBenchID; ?> / Configuration:  / Plot Version #<?php echo $plot->PlotVersion; ?>';
      
      var options = {
        chart: {
          renderTo: 'container',
          zoomType: 'x',
          resetZoomButton: {
            position: {
              // align: 'right', // by default
              // verticalAlign: 'top', // by default
              x: 50,
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
          text: '',
          x: -20
        },
        xAxis: {
          title: {
            text: 'Milliseconds',
            margin: 15
          },
          tickInterval: 7,
          labels: 
          {
            rotation: -90,
            align: 'right',
            style: {
                fontSize: '10px',
                fontFamily: 'Helvetica, sans-serif'
            }
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
          },
          
          crosshairs:true
        },
        plotOptions: {
            series: {
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 3,
                    states: {
                      hover: {
                        enabled: true
                      }
                      
                    }
                }
            }
        },
        legend: {
          enabled: false,
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'top',
          x: -10,
          y: 100,
          borderWidth: 0
        },
        series: {
          data: []
        }
        
      };


      chart = new Highcharts.Chart(options);


      
      
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
        
      });
      

<?php
if ($plotID == 0)
{
  redirect('simviz', 'refresh');
} else if ($plotID != 0)
{
  ?>
          
  var settings = <?php echo $plot->PlotSettings; ?>;
  console.log(settings);
  $("#plotSettings").html(settings);
  var setName = settings.series.name;
  var finName = setName.split(".").join("__");

  doChartYDraw('<?php echo base_url(); ?>include/'+settings.series.data_link, finName);
  $('#y'+finName).prop("checked", true);

  //          setName = settings.xaxis;
  //          finName = setName.split(".").join("__");
  //doChartXDraw('<?php echo base_url(); ?>include/'+settings.series.data_link, finName);
  chart.redraw();

  $('#x'+finName).prop("checked", true);

  //chart.xAxis[0].setExtremes(0, 50);
  chart.showResetZoom();
                            
  <?php
}
?>
    $(".yaxisvar").live("click", function()
    {

      var data_link = includeFilePath+'data/testbenches/DriveTrain/<?php echo$conf->confFolderName;?>/'+$(this).val();
      console.log(data_link);
      var name = $(this).attr("seriesName");
      var seriesShortName = $(this).attr("seriesShortName");
      var id = $(this).attr("id");
      id = id.substring(1);

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
          //socket.emit('sendseries', message);
        }

        doChartYDraw(id, name,seriesShortName, data_link);
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
          //socket.emit('sendseries', message);
        }

        //name = name.split("__").join(".");
        //remove series from chart
        var series = chart.series;

        for (var i = 0; i < series.length; i++)
        {
          console.log("series name: "+series[i].name);
          if (series[i].name == seriesShortName)
          {
            chart.series[i].remove(true);
            break;
          }
        }

        receiver = 0;
      }


    });

    function doChartYDraw(id, seriesName, seriesShortName, jsonFilePath)
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

        console.log(time);

        console.log('Looking for SeriesName: '+seriesName);

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
                series.data.push(obj2);
                
                var timeAdj = time[j]*100;
                var timeArray = String(timeAdj).split('.');
                
                categories.push(timeArray[0]);
              }
              pCounter++;
            });

            series.name = seriesShortName;
            chart.xAxis[0].setCategories(categories);
            chart.addSeries(series);

            console.log("Series Added to Chart object");
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
//  var socket = io.connect('http://gamewisp.com:8086');
//
//  // on connection to server, ask for user's name with an anonymous callback
//  socket.on('connect', function(){
//    // call the server-side function 'adduser' and send one parameter (value of prompt)
//
//  });
//
//  socket.on('updatechart', function (data) {
//    //console.log(data);
//    var json = $.parseJSON(data);
//
//    console.log("receive Message to change zoome");
//    chart.xAxis[0].setExtremes(json.low, json.high);
//    chart.showResetZoom();
//
//  });
//
//  socket.on('updatecheck', function (data) {
//    //console.log(data);
//    var json = $.parseJSON(data);
//
//    console.log("receive Message to check: "+"#y"+json.ySeriesID);
//    receiver = 1;
//
//    checked = json.checked;
//    console.log($("#y"+json.ySeriesID).val());
//
//    $("#y"+json.ySeriesID).trigger('click');
//
//    if (json.checked == 1)
//      $("#y"+json.ySeriesID).attr('checked', true);
//    else
//      $("#y"+json.ySeriesID).attr('checked', false);
//
//  });


  // on load of page
  $(function(){

    $('#zoomSend').click( function() {

      console.log("high"+$('#extremeHigh').html());
      console.log("high"+$('#extremeHigh').val());
      console.log("high"+$('#extremeHigh').text());

      var message = '{ "low" : ' +$('#extremeLow').html()+', "high" : '+$('#extremeHigh').html()+'}';


      //$('#data').val('');
      // tell server to execute 'sendchat' and send along one parameter
      //socket.emit('sendzoom', message);
    });

  });


  function thingy()
  {

  }

  $( ".uSlider" ).slider({ /* Slider with minimum */
		range: "min",
		value: 25,
		min: 1,
		max: 100,
		slide: function( event, ui ) {
			//$( "#minRangeAmount" ).val( "$" + ui.value );
		}
	});

});
</script>

<div class="fluid">
  <div class="widget grid12">
    <div class="whead"><h6>Plot</h6><div class="clear"></div></div>
    <div class="body">
      <div id="container" style=""></div>
      
    </div>
  </div>

  
</div>
<div class="fluid">
  <div class="grid8">
  <div class="widget">
    <div class="whead"><h6>Graph Series</h6>
      <ul class="titleToolbar">
          <li><a id="AddVariableDialog_open" href="#_" class="">Add Variables</a></li>
          <li><a id="AddVariableDialog_open" href="#_" class="">Clear Variables</a></li>
<!--          <li><a id="AddVariableDialog_open" href="#_" class="">Save Plot</a></li>
          <li><a id="AddVariableDialog_open" href="#_" class="">Save New Plot</a></li>-->
      </ul>
      <div class="clear"></div></div>
    <div class="body">
      <ul id="yaxislist">
        Add Some Variables to Begin	
      </ul>

      <!--      <div id="demo2" style="height:100px;">
              
            </div>-->
      <script type="text/javascript">
        //Setup The JSTree
        $(function () {
          $("#demo2").jstree({
            "plugins" : ["themes", "json_data", "ui"],
            "json_data" : {
              "ajax" : {

                // the URL to fetch the data
                "url" : "<?php echo base_url(); ?>graph/getTreeJSON"

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
          //          $.post("<?php echo base_url(); ?>graph/getTopTreeJSON", {}, function(result){
          //            text = result;
          //            console.log(text);
          //            
          //          });

          
        });
      </script>



    </div>
  </div>
  <div class="widget">
    <div class="whead"><h6>X-Axis</h6><div class="clear"></div></div>
    <div class="body">
      <ul>
        <li>
          <input type="radio" name="xaxisoption" id="xTime" value="<?php echo base_url();?>include/<?php echo $plot->PlotDataStoreLocation;?>/DriveTrain_cfg1_data_chunk0.json" class="xaxisvar" checked />
          <label for="xTime" class="eltLabel">Time</label>
        </li>
      </ul>
    </div>
  </div>
  </div>
  <div class="grid4">
    <div class="widget">
      <div class="whead"><h6>Plotting History</h6><div class="clear"></div></div>
      <div id="dyna" class="hiddenpars">
        <a class="tOptions" title="Options"><img src="<?php echo base_url(); ?>include/images/icons/options" alt="" /></a>
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
            foreach ($plots as $plt)
            {
              ?>
              <tr class="gradeX">
                <td><a href="http://mdanderson.me/projects/isis/plotutil/simviz/plot/<?php echo $plt->PlotID; ?>">Version <?php echo $plt->PlotVersion; ?></a></td>
                <td><?php echo date("D M d, Y", strtotime($plt->PlotCreateDate)); ?></td>
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
  


  <!--Data-->
  <div id="pointRatio" style="display:none;">25</div>
  <div id="treeSelectedDataLink" style="display:inline;"></div>
  <div id="treeSelectedName" style="display:inline;"></div>

</div>


<script type="text/javascript">
  
  $(document).ready(function() {
  
    //Global Variables
    var plotSettings = <?php echo $plot->PlotSettings; ?>;
    $("#plotSettings").html(''+plotSettings);

    //Add variable / Variable Search Functions
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
      $('#AddVariableDialog').dialog('open');
      return false;
    });
  
    //Event triggered on Search Button Click
    $("#find").live('click',function(e)
    {
      $("#barLoader").show();
      e.preventDefault();
      var variablesearch = $.trim($("#variableSerach").val());
    
      var post_data = {
        variablesearch : variablesearch
      };
    
      $.post('<?php echo base_url(); ?>index.php/simviz/getSearchResults/<?php echo $plot->PlotID;?>', post_data, function(result)
      {
        $("#searchResults").html(result);
        $("#barLoader").hide();
      })
    });
  
    $(".varbox").live("click", function(e)
    {
      var id = $(this).attr("id");
      var name = $(this).attr("name");
      var shortname = $(this).attr("shortname");
      var datalink = $(this).attr("data_link");
    
      $(this).css('background-color', '#ccc');
    
      var newCheckBox = "";
    
      newCheckBox += '<li>';
      newCheckBox += '<input type="checkbox" name="elements[]" id="y'+id+'" seriesName="'+name+'" seriesShortName="'+shortname+'" value="'+datalink+'" class="yaxisvar" />';
      newCheckBox += '<label for="y'+id+'" class="eltLabel">'+shortname+'</label>';
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
      <img id="barLoader" src="<?php echo base_url();?>include/images/bar_loader.gif" style="display:none;"/>
      <div id="searchResults" style="margin: 3px 0px 3px 3px;" class="scrollpane">
        
      </div>
    </div>
  </div>
</div>







