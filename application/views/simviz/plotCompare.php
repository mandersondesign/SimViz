<?php
$plotID = $plot->PlotID;
?>
<script src="<?php echo base_url(); ?>include/js/highcharts.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>include/js/modules/exporting.src.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>include/js/plugins/jstree/_lib/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>include/js/plugins/jstree/_lib/jquery.hotkeys.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>include/js/plugins/jstree/jquery.jstree.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>include/js/knockout-2.1.0.js"></script>

<div id="extremeLow" style="display:none;">0</div>
<div id="extremeHigh" style="display:none;">1000</div>
<button id="zoomSend" style="display:none;"></button>
<div class="fluid">
  <div class="widget grid12">
    <div class="whead"><h6>Plot</h6><div class="clear"></div></div>
    <div class="body">
      <div id="container" style=""></div>
      <div class="clear"></div>
      <div id="container2" style="margin-top: 20px;"></div>
    </div>
  </div>
</div>

<script type="text/javascript">
  
  //var chart; // global
  var receiver = 0;
  var checked = 0;
  
  
  $(document).ready(function() {
    $.ajaxSetup({ cache: false });
  
    function SettingsItem(json) {
      var self = this;
         
      self.ps_id = ko.observable(json.ps_id);
      self.user_id = ko.observable(json.user_id);
      self.user_name = ko.observable(json.user_name);
      self.user_picture = ko.observable(json.user_picture);
      
      self.game_id = ko.observable(json.game_id);
      self.game_name = ko.observable(json.game_name);
      self.game_image = ko.observable(json.game_image);
      
      //Comments
      self.shouldShowCommentsLoading = ko.observable(true);
      self.shouldShowCommentsContainer = ko.observable(false);
      
      self.shouldShowCommentLoader = ko.observable(false);
      
      var mappedComments = $.map(json.content.comments, function(item) { return new Comment(item) });
      self.comments = ko.observable(mappedComments);
      
      self.commentReply = ko.observable();
      self.activeComment = ko.observable();
    
      //Votes
      self.votes = ko.observable(json.content.votes);
      self.reports = ko.observable(json.content.reports);
      
      self.shouldShowCommentsLoading(false);
      self.shouldShowCommentsContainer(true);
      
      self.formattedVotes = ko.computed(function() {
        return self.up_votes() +'/'+self.down_votes()+' ('+self.votes_ratio()*100+'%)';
      });
    }
    
    function Plot(json) {
      var self = this;
         
      self.plot_id = ko.observable(json.plot_id);
      self.sim_id = ko.observable(json.sim_id);
      self.sim_name = ko.observable(json.sim_name);
      self.config_id = ko.observable(json.config_id);
      self.config_name = ko.observable(json.config_name);
      self.config_folder_name = ko.observable(json.config_folder_name);
      self.testbench_id = ko.observable(json.testbench_id);
      self.testbench_name = ko.observable(json.testbench_name);
      self.testbench_folder_name = ko.observable(json.testbench_folder_name);
      self.testbench_desc = ko.observable(json.testbench_desc);
      
      self.plot_version = ko.observable(json.plot_version);
      self.plot_json = ko.observable(json.plot_json);
      self.plot_settings = ko.observable(json.plot_settings);
      self.create_date = ko.observable(json.create_date);
      
      self.last_update_date = ko.observable(json.last_update_date);
      self.plot_tree_location = ko.observable(json.plot_tree_location);
      self.plot_data_store_location = ko.observable(json.plot_data_store_location);
      
      //Booleans
      self.shouldShowCommentsLoading = ko.observable(true);
      self.shouldShowCommentsContainer = ko.observable(false);
      self.shouldShowCommentLoader = ko.observable(false);
      
      
      //console.log(json);
      //Computables
     
      
      //      self.formattedDate = ko.computed(function() {
      //        return Date.parse(self.create_date().toString()).toString('hh:mm tt - M/dd/yy');
      //      });

    }
    
    function Variable(data) {
      var self = this;
    
      self.variable_id = ko.observable(data.variable_id);
      self.name = ko.observable(data.name);
      self.short_name = ko.observable(data.short_name);
      self.data_link = ko.observable(data.data_link);
      self.plot_id = ko.observable(data.plot_id);
      self.type = ko.observable(data.type);
    }
    
    function ComparePlotViewModel() {
      // Data
      var self = this;

      //View Model Variables
      self.chart = ko.observable();
      self.chart2 = ko.observable();
      self.plot = ko.observable();
      self.plot2 = ko.observable();
      
      self.timeSeriesVar = ko.observable();
      
      self.variables = ko.observableArray();
      self.searchVariables = ko.observableArray();
      self.checkedVariables = ko.observableArray();
      
      self.includeFilePath = "<?php echo base_url(); ?>";
      
      self.initPlot = function()
      {
        var plotID = '<?php echo $plot->PlotID; ?>';
        
        var post_data = {
          plotID: plotID
        };
        
        $.post("<?php echo base_url(); ?>index.php/simviz/getPlotJSON/", post_data, function(result)
        {
          var json = $.parseJSON(result);
          self.plot(new Plot(json));
          
          console.log(self.plot().plot_id());
          
          self.initTime();
          self.initializeChart();
          self.initPlot2();
          
        });
      };
      
      self.initPlot2 = function()
      {
        var plotID = '<?php echo $plot2->PlotID; ?>';
        
        var post_data = {
          plotID: plotID
        };
        
        $.post("<?php echo base_url(); ?>index.php/simviz/getPlotJSON/", post_data, function(result)
        {
          var json = $.parseJSON(result);
          self.plot2(new Plot(json));
          
          console.log(self.plot2().plot_id());
          
          self.initializeChart2();
          
        });
      };
      
      
      self.initTime = function()
      {
        var plotID = '<?php echo $plot->PlotID; ?>';
        
        var post_data = {
          plotID: plotID
        };
        
        $.post("<?php echo base_url(); ?>index.php/simviz/getTimeSeries/", post_data, function(result)
        {
          var json = $.parseJSON(result);
          self.timeSeriesVar(new Variable(json));
          
          //console.log(self.timeSeriesVar().variable_id());
        });
      };
      
      
      self.initializeChart = function()
      {
        var chartTitle = 'TestBench' + self.plot().testbench_name() + ' / Configuration: '+self.plot().config_name()+' / Plot Version #' + self.plot().plot_version();
        var options = {
          chart: {
            renderTo: 'container',
            zoomType: 'x',
            resetZoomButton: {
              position: {x: 50, y: -30}},
            type: 'line',
            marginRight: 130,
            marginBottom: 75,
            events: {
              selection: function(event) {
                if (event.xAxis != null)
                {
                  var preMin = self.chart().xAxis[0].min;
                  var preMax = self.chart().xAxis[0].max;
                  var postMin = event.xAxis[0].min;
                  var postMax = event.xAxis[0].max;
                  console.log(self.chart().xAxis[0].min);
                  console.log(self.chart().xAxis[0].max);
                  console.log(event.xAxis[0].min);
                  console.log(event.xAxis[0].max);
                  var maxes = Math.abs(postMax-preMax);
                  var mins = Math.abs(postMin-preMin);
                  console.log(maxes);
                  console.log(mins);
                  var diff = maxes / (mins);
                  console.log("Diff:"+diff);
                  console.log("Chart Interval Pre: "+self.chart().xAxis[0].tickInterval);
                  self.chart().xAxis[0].tickInterval = self.chart().xAxis[0].tickInterval / diff;
                  self.chart().redraw();
                  console.log("Chart Interval Post: "+self.chart().xAxis[0].tickInterval);
                  console.log(Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].min),
                  Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].max));
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
          xAxis: {
            title: {text: 'Milliseconds', margin: 15},
            tickInterval: 7,
            labels:
              {
              rotation: -90,
              align: 'right',
              style: {fontSize: '10px', fontFamily: 'Helvetica, sans-serif'}
            }
          },
          title: {text: chartTitle, x: -20},
          yAxis: {
            title: {text: 'Value'},
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
                states: {hover: {enabled: true}}
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
          series: {data: []}
        };
        
        self.chart(new Highcharts.Chart(options));
      };
      
      self.initializeChart2 = function()
      {
        var chartTitle = 'TestBench' + self.plot2().testbench_name() + ' / Configuration: '+self.plot2().config_name()+' / Plot Version #' + self.plot2().plot_version();
        var options =({
          chart: {
            renderTo: 'container2',
            type: 'line',
            zoomType: 'x',
            marginRight: 130,
            marginBottom: 75,
            resetZoomButton: {
              position: {x: 50, y: -30}
            },
            events: {
              selection: function(event1) {
                if (event1.xAxis != null)
                {
                  var preMin = self.chart2().xAxis[0].min;
                  var preMax = self.chart2().xAxis[0].max;
                  var postMin = event1.xAxis[0].min;
                  var postMax = event1.xAxis[0].max;
                  console.log(self.chart2().xAxis[0].min);
                  console.log(self.chart2().xAxis[0].max);
                  console.log(event1.xAxis[0].min);
                  console.log(event1.xAxis[0].max);
                  var maxes = Math.abs(postMax-preMax);
                  var mins = Math.abs(postMin-preMin);
                  console.log(maxes);
                  console.log(mins);
                  var diff = maxes / (mins);
                  console.log("Diff:"+diff);
                  console.log("Chart Interval Pre: "+self.chart2().xAxis[0].tickInterval);
                  self.chart2().xAxis[0].tickInterval = self.chart2().xAxis[0].tickInterval / diff;
                  self.chart2().redraw();
                  console.log("Chart Interval Post: "+self.chart2().xAxis[0].tickInterval);
                  console.log(
                  Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event1.xAxis[0].min),
                  Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event1.xAxis[0].max)
                );
                  console.log(event1.yAxis[0].min, event1.yAxis[0].max);
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
          title: {
            text: chartTitle,
            x: -20 // center
          },
          xAxis: {
            title: {text: 'Milliseconds', margin: 15},
            tickInterval: 7,
            labels:
              {
              rotation: -90,
              align: 'right',
              style: {fontSize: '10px', fontFamily: 'Helvetica, sans-serif'}
            }
          },
          yAxis: {
            title: {text: 'Value'},
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
              }]
          },
          tooltip: {
            formatter: function() {
              return '<b>'+ this.series.name +'</b><br/>'+ Math.round(this.x)  +' msec | '+ this.y +'';
            },
            crosshairs:true
          },
          plotOptions: {
            series: {
              marker: {
                enabled: false,
                symbol: 'circle',
                radius: 3,
                states: {hover: {enabled: true}}
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
          series: {data: []}
        });
      
        
        self.chart2(new Highcharts.Chart(options));
      };
      
      
      self.initChartSettings = function()
      {
        var settings = <?php echo $plot->PlotSettings; ?>;
        console.log(settings);
        
        $("#plotSettings").html(settings);
        var setName = settings.series.name;
        var finName = setName.split(".").join("__");
        doChartYDraw('<?php echo base_url(); ?>include/'+settings.series.data_link, finName);
        $('#y'+finName).prop("checked", true);
        chart.redraw();
        $('#x'+finName).prop("checked", true);
        chart.showResetZoom();
      };
      
      self.doChartYDraw = function(yvar)
      {
        var settings = self.plot().plot_settings();
        var pl2_settings = self.plot2().plot_settings();
        
        var name = yvar.name();
        var seriesShortName = yvar.name();
        var id = yvar.variable_id();
        
        
        var data_link = "";
        var pl2_data_link = "";
        
        
        //get time
        var time_data_link = self.includeFilePath+settings.data_folder+'/'+self.timeSeriesVar().data_link();
        var time = new Array();
        
        //console.log(yvar.data_link());
        
        $.getJSON(time_data_link, function(json) {
          $.each(json, function (i, obj) {
            
            //console.log(obj);
            
            if (obj.name == "time")
            {
              //console.log(obj.name);
              data_link = self.includeFilePath+settings.data_folder+'/'+obj.data_link;
              
              $.getJSON(data_link, function(ts_json) {
                $.each(ts_json[obj.index], function (j, obj2) {time.push(obj2);});
                console.log(time);
                
                //Once we have time, do awesome things
                if (yvar.data_link().indexOf("var") > -1)
                {
                  console.log("var");
                  var var_data_link = self.includeFilePath+settings.data_folder+'/'+yvar.data_link();
                  
                  //console.log(var_data_link);
                  $.getJSON(var_data_link, function(json) {
                    
                    console.log(name);
                    
                    $.each(json, function (i, varObj) {
                      if (varObj.name == name)
                      {
                        console.log(varObj.name);
                        data_link = self.includeFilePath+settings.data_folder+'/'+varObj.data_link;
  
                        $.getJSON(data_link, function(json) {
                          console.log("getJson");
                          var categories = [];
                          var pointRatio = $("#pointRatio").html();
    
                          console.log('Looking for SeriesName: '+name);
                          var series = {data: []}
                          var pCounter = 0;
                          
                          $.each(json[varObj.index], function (j, obj2) {
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
                          self.chart().xAxis[0].setCategories(categories);
                          self.chart().addSeries(series);
                          console.log("Series Added to Chart object");
                          self.chart().redraw();
                        })
                        .error(function(request, err)
                        {
                          console.log('error');
                          console.log(err);
                        });

                        return false;
                      }
                    });
                  });
                }
                else if (yvar.data_link().indexOf("param") > -1)
                {
                  console.log("param");
                  var var_data_link = self.includeFilePath+settings.data_folder+'/'+yvar.data_link();
                  //console.log(var_data_link);
                  $.getJSON(var_data_link, function(json) {
                    
                    console.log(name);
                    
                    $.each(json, function (i, varObj) {
                      if (varObj.name == name)
                      {
                        console.log("getJson");
                        var categories = [];
                        var pointRatio = $("#pointRatio").html();
    
                        console.log('Looking for SeriesName: '+name);
                        var series = {data: []}
                        var pCounter = 0;
                          
                          
                        var objValue = varObj.value;
                          
                        $.each(time, function (j, obj2) {
                          var arr = new Array();
                          if (pCounter%pointRatio == 0)
                          {
                            series.data.push(objValue);
                            var timeAdj = time[j]*100;
                            var timeArray = String(timeAdj).split('.');
                            categories.push(timeArray[0]);
                          }
                          pCounter++;
                        });
                          
                        series.name = seriesShortName;
                        self.chart().xAxis[0].setCategories(categories);
                        self.chart().addSeries(series);
                        console.log("Series Added to Chart object");
                        self.chart().redraw();

                        return false;
                      }
                    });
                  });
                }
                return false;
              });
              
              
              //SecondGraph
              data_link = self.includeFilePath+pl2_settings.data_folder+'/'+obj.data_link;
              
              $.getJSON(data_link, function(ts_json) {
                $.each(ts_json[obj.index], function (j, obj2) {time.push(obj2);});
                //console.log(time);
                
                //Once we have time, do awesome things
                if (yvar.data_link().indexOf("var") > -1)
                {
                  console.log("var");
                  var var_data_link = self.includeFilePath+settings.data_folder+'/'+yvar.data_link();
                  
                  console.log(var_data_link);
                  $.getJSON(var_data_link, function(json) {
                    
                    console.log(name);
                    
                    $.each(json, function (i, varObj) {
                      if (varObj.name == name)
                      {
                        console.log(varObj.name);
                        data_link = self.includeFilePath+pl2_settings.data_folder+'/'+varObj.data_link;
  
                        $.getJSON(data_link, function(json) {
                          console.log("getJson");
                          var categories = [];
                          var pointRatio = $("#pointRatio").html();
    
                          console.log('Looking for SeriesName: '+name);
                          var series = {data: []}
                          var pCounter = 0;
                          
                          $.each(json[varObj.index], function (j, obj2) {
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
                          self.chart2().xAxis[0].setCategories(categories);
                          self.chart2().addSeries(series);
                          console.log("Series Added to Chart object");
                          self.chart2().redraw();
                        })
                        .error(function(request, err)
                        {
                          console.log('error');
                          console.log(err);
                        });

                        return false;
                      }
                    });
                  });
                }
                else if (yvar.data_link().indexOf("param") > -1)
                {
                  console.log("param");
                  var var_data_link = self.includeFilePath+pl2_settings.data_folder+'/'+yvar.data_link();
                  //console.log(var_data_link);
                  $.getJSON(var_data_link, function(json) {
                    
                    console.log(name);
                    
                    $.each(json, function (i, varObj) {
                      if (varObj.name == name)
                      {
                        console.log("getJson");
                        var categories = [];
                        var pointRatio = $("#pointRatio").html();
    
                        console.log('Looking for SeriesName: '+name);
                        var series = {data: []}
                        var pCounter = 0;
                          
                          
                        var objValue = varObj.value;
                          
                        $.each(time, function (j, obj2) {
                          var arr = new Array();
                          if (pCounter%pointRatio == 0)
                          {
                            series.data.push(objValue);
                            var timeAdj = time[j]*100;
                            var timeArray = String(timeAdj).split('.');
                            categories.push(timeArray[0]);
                          }
                          pCounter++;
                        });
                          
                        series.name = seriesShortName;
                        self.chart2().xAxis[0].setCategories(categories);
                        self.chart2().addSeries(series);
                        console.log("Series Added to Chart object");
                        self.chart2().redraw();

                        return false;
                      }
                    });
                  });
                }
                return false;
              });
              
            }
          });
        });
      };
      
      self.doChartXDraw = function(jsonFilePath, name)
      {
        name = name.split("__").join(".");
        $.getJSON(jsonFilePath, function(json) {
          var categories = [];
          $.each(json.variables, function (i, obj) {
            if (obj.name == name)
            {
              var series = {data: []}
              var pCounter = 0;
              $.each(obj.data, function (j, obj2) {
                if (pCounter%25 == 0)
                  categories.push(obj2);
                pCounter++;
              });
              chart.xAxis[0].setCategories(categories);
            }
          });
        });
      };
      
      self.findClick = function()
      {
        $("#barLoader").show();
        var variablesearch = $.trim($("#variableSerach").val());

        var post_data = {
          variablesearch : variablesearch
        };

        $.post('<?php echo site_url(); ?>/simviz/getSearchResults/'+self.plot().plot_id(), post_data, function(result)
        {
          
          var json = $.parseJSON(result);
          //console.log(json);
          var mappedVars = $.map(json.variables, function(item) { return new Variable(item) });
          self.searchVariables(mappedVars);
          
          //$("#searchResults").html(result);
          $("#barLoader").hide();
        })
      };
      
      self.varBoxClick = function(clckVar)
      {
        self.searchVariables.remove(clckVar);
        self.variables.push(clckVar);

        //        $(this).css('background-color', '#ccc');
        //        var newCheckBox = "";
        //        newCheckBox += '<li>';
        //        newCheckBox += '<input type="checkbox" name="elements[]" id="y'+id+'" seriesName="'+name+'" seriesShortName="'+shortname+'" value="'+datalink+'" class="yaxisvar" />';
        //        newCheckBox += '<label for="y'+id+'" class="eltLabel">'+shortname+'</label>';
        //        newCheckBox += '</li>';
        //        $("#yaxislist").append(newCheckBox);
      };
      
      self.initVariableDialog = function()
      {
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

        $('#ClearVariable').live("click", function () {
          for (var i = 0; i < series.length; i++)
          {
            console.log("series name: "+ series[i].name);
            chart.series[i].remove(true);
          }
        });
      };
      
      self.clickXAxisVariable = function(xvar)
      {
        var data_link = $(this).val();
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
      };
      
      self.clickYAxisVariable = function(yvar)
      {
        
        var settings = self.plot().plot_settings();
        var name = yvar.name;
        var seriesShortName = yvar.name;
        var id = yvar.variable_id;
        
        if( !$("#y"+id).is(':checked') )
        {
          $("#y"+id).prop('checked', true);
          console.log("checked!");
          self.doChartYDraw(yvar);
          self.chart().redraw();
        }
        else
        {
          console.log("attempt remove");

          var series = self.chart.series;
          for (var i = 0; i < series.length; i++)
          {
            console.log("series name: "+ series[i].name);
            if (series[i].name == seriesShortName)
            {
              self.chart.series[i].remove(true);
              break;
            }
          }
        }
      };
      
      //Init Calls
      self.initPlot();
      self.initVariableDialog();
      //self.initChartSettings();
      
    }

    var plotViewModel = new ComparePlotViewModel();
    ko.applyBindings(plotViewModel);
  
  });
</script>




<script type="text/javascript">

  $(function () {
    $(document).ready(function() {
      
      vTable = $('.vTable').dataTable({
        "bJQueryUI": false,
        "bAutoWidth": false,
        "sDom": '<"H"fl>t<"F"ip>'
      });

      $('#dyna .tOptions').click(function () {
        $('#dyna .tablePars').slideToggle(200);
      });

      $('.tOptions').click(function () {
        $(this).toggleClass("act");
      });
    });
    
    $(function(){

      $('#zoomSend').click( function() {

        console.log("high"+$('#extremeHigh').html());
        console.log("high"+$('#extremeHigh').val());
        console.log("high"+$('#extremeHigh').text());

        var message = '{ "low" : ' +$('#extremeLow').html()+', "high" : '+$('#extremeHigh').html()+'}';
      });
    });
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
  <div class="grid8">
    <div class="widget">
      <div class="whead"><h6>Graph Series</h6>
        <ul class="titleToolbar">
          <li><a id="AddVariableDialog_open" href="#_" class="">Add Variables</a></li>
          <li><a id="ClearVariable" href="#_" class="">Clear Variables</a></li>
        </ul>
        <div class="clear"></div></div>
      <div class="body">
        <ul id="yaxislist" data-bind="foreach: variables">
          <!--          <li>Add Some Variables to Begin</li>-->

          <li>
            <input type="checkbox" 
                   data-bind="click: $parent.clickYAxisVariable, attr: { id: 'y'+variable_id() }" />
            <label for="y'+id+'" class="eltLabel" data-bind="text: name"></label>
          </li>

        </ul>
        <script type="text/javascript">
          //Setup The JSTree
          $(function () {
            $("#demo2").jstree({
              "plugins" : ["themes", "json_data", "ui"],
              "json_data" : {
                "ajax" : {
                  "url" : "<?php echo site_url(); ?>/simviz/graph/getTreeJSON"
                }
              }
            }).bind("select_node.jstree", function (event, data) {
              var selectedObj = data.rslt.obj;
              $("#treeSelectedDataLink").html(''+selectedObj.attr("data_link"));
              $("#treeSelectedName").html(''+selectedObj.attr("name"));
              $("#treeSelectedDataLink").trigger("click");
            });
          });
        </script>

      </div>
    </div>
    <div class="widget">
      <div class="whead"><h6>X-Axis</h6><div class="clear"></div></div>
      <div class="body">
        <ul>
          <li>
            <input type="radio" name="xaxisoption" id="xTime" value="<?php echo base_url(); ?>include/<?php echo $plot->PlotDataStoreLocation; ?>/DriveTrain_cfg1_data_chunk0.json" class="xaxisvar" checked />
            <label for="xTime" class="eltLabel">Time</label>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="grid4">
    <div class="widget">
      <div class="whead"><h6>Plots</h6><div class="clear"></div></div>
      <div id="dyna" class="hiddenpars">
        <a class="tOptions" title="Options"><img src="<?php echo base_url(); ?>include/images/icons/options" alt="" /></a>
        <table cellpadding="0" cellspacing="0" border="0" class="vTable" id="dynamicVTable">
          <thead>
            <tr>
              <th>Plot Name<span class="sorting" style="display: block;"></span></th>
              <th>Created</th>
            </tr>
          </thead>
          <tbody>

            <tr class="gradeX">
              <td><a href="<?php echo site_url(); ?>/plot/<?php echo $plot->PlotID; ?>">Sim <?php echo $plot->PlotSimName; ?> - Version <?php echo $plot->PlotVersion; ?></a></td>
              <td><?php echo date("D M d, Y", strtotime($plot->PlotCreateDate)); ?></td>
            </tr>
            <tr class="gradeX">
              <td><a href="<?php echo site_url(); ?>/plot/<?php echo $plot2->PlotID; ?>">Sim <?php echo $plot2->PlotSimName; ?> - Version <?php echo $plot2->PlotVersion; ?></a></td>
              <td><?php echo date("D M d, Y", strtotime($plot2->PlotCreateDate)); ?></td>
            </tr>

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

    

    
  });
</script>

<div id="AddVariableDialog" class="dialog" title="Add Variable to Plot">
  <h3>Search for Variables</h3>
  <div class="fluid">
    <div class="widget grid12">
      <div class="searchLine" style="margin-top:0px;">
        <form action="">
          <button type="submit" name="find" id="find" value="" data-bind="click: findClick"><span class="icos-search"></span></button>
          <input type="text" name="search" id="variableSerach" class="" placeholder="Enter search text..." />
        </form>
      </div>
      <img id="barLoader" src="<?php echo base_url(); ?>include/images/bar_loader.gif" style="display:none;"/>
      <div id="searchResults" style="margin: 3px 0px 3px 3px;" class="scrollpane" data-bind="foreach: searchVariables">

        <div class="fluid varbox" style="margin:5px 5px 5px 0px; padding:0 0px 0 5px; border-bottom:1px #ccc solid;" 
             data-bind="attr: { id: variable_id, name: name, shortname: short_name, data_link: data_link, var_type: type }, click: $parent.varBoxClick" >
          <div class="grid12"  class="" style="font-size: 11px;" data-bind="text: name"><div class="clear"></div></div></div>

      </div>
    </div>
  </div>
</div>





