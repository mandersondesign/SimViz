<script>
  $(document).ready(function() {

    $("#new_tb").show();
    $("#new_configuration").show();

    $("#select_tb").change(function() {
      if ($(this).val() == 0)
      {
        $("#new_tb").show();
      }
      else
      {
        $("#new_tb").hide();
        //go get configs for that TB and fill select
      }
    });

    $("#select_config").change(function() {
      if ($(this).val() == 0)
      {
        $("#new_configuration").show();
      }
      else
      {
        $("#new_configuration").hide();
        //go get configs for that TB and fill select
      }
    });
  
  });



</script>

<form id="simForm" method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>index.php/simviz/addNewSimulation" class="main validate">
  <fieldset>
    <div class="widget fluid">
      <div class="whead"><h6>Add A New Simulation Mat File:</h6><div class="clear"></div></div>


      <div id="testbench" class="validate_any">


        <div class="formRow"  id="exist_testbench">

          <div class="grid3"><label>Testbench Name:</label> </div>
          <div class="grid9">
            <select id="select_tb" name="select_tb" class="required">
              <option value="0">Select One</option>
              <?php
              foreach ($testbenches as $tb)
              {
                echo '<option value="' . $tb->tbID . '">' . $tb->tbName . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>
        <div class="formRow"  id="new_tb">
          <div class="grid3"><label>or Enter a New Name:</label></div>
          <div class="grid9"><input type="text" name="new_testbench" class="required"/></div>
          <div class="clear"></div>
        </div>
      </div>

      <div id="configuration" class="validate_any">


        <div class="formRow" id="exist_configuration">
          <div class="grid3"><label>Configuration Name:</label></div>
          <div class="grid9">
            <select id="select_config" name="select_config" class="required">
              <option value="0">Select One</option>
              <?php
              foreach ($configurations as $cfg)
              {
                echo '<option value="' . $cfg->confID . '">' . $cfg->confName . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>
        <div class="formRow" id="new_configuration">
          <div class="grid3"><label>or Name a New Configuration:</label></div>
          <div class="grid9">
            <input type="text" name="new_config" class="required"/>
          </div>
          <div class="clear"></div>
        </div>
      </div>

      <div class="formRow" id="configuration">
        <div class="grid3"><label>Simulation Name:</label></div>
        <div class="grid9"><input type="text" name="new_simulation" class="required"/></div>
        <div class="clear"></div>
      </div>

      <br/>
      <div class="formRow">
        <div class="grid3"><label>Select MAT File:</label></div>
        <div class="grid9">
          <input type="file" name="upfile" size="20" class="required">

        </div>
        <div class="clear"></div>
      </div>
      <div class="formRow">
        <input id="LoadingDialog_open" type="submit" value="Add New File" class="buttonM bGreen mb10 mt5" >
      </div>

    </div>
  </fieldset>
</form>

<script>
  $(document).ready(function() {
    //Add variable / Variable Search Functions
    $('#LoadingDialog').dialog({
      autoOpen: false,
      width: 300,
      height: 200,
      buttons: {}
    });

    $('#LoadingDialog_open').live("click", function (e) {
      //e.preventDefault();
      $('#LoadingDialog').dialog('open');
      $('#simForm').submit();
    });
  });
</script>


<div id="LoadingDialog" class="dialog" title="Importing and Converting MAT File">
  <h3>Converting Mat File</h3>
  <div class="fluid">
    <div class="widget grid12">
      <img id="barLoader" src="<?php echo base_url(); ?>include/images/circle_loader.gif" />
    </div>
  </div>
</div>



