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

<form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>index.php/compare/redirectToNextStep" class="main">
  <fieldset>
    <div class="widget fluid">
      <div class="whead"><h6>Choose a Testbench & 2 Configurations:</h6><div class="clear"></div></div>

      <div id="testbench">
        <div class="formRow" id="exist_testbench">

          <div class="grid3"><label>Testbench:</label> </div>
          <div class="grid9">
            <select id="select_tb" name="select_tb">
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
      </div>

      <div id="configuration">
        <div class="formRow" id="exist_configuration">
          <div class="grid3"><label>First Configuration:</label></div>
          <div class="grid9">
            <select id="select_config" name="select_config">
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
      </div>
      
      <div id="configuration2">
        <div class="formRow" id="exist_configuration2">
          <div class="grid3"><label>Second Configuration:</label></div>
          <div class="grid9">
            <select id="select_config2" name="select_config2">
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
      </div>
      
      <div class="formRow">
        <input type="submit" value="Add New File" class="buttonM bGreen mb10 mt5" >
      </div>

    </div>
  </fieldset>
</form>
