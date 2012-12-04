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

<form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>index.php/compare/redirectToCompare" class="main">
  <fieldset>
    <div class="widget fluid">
      <div class="whead"><h6>Choose Plots:</h6><div class="clear"></div></div>

      <div id="simulation">
        <div class="formRow" id="exist_sim">
          <div class="grid3"><label>Plot from First Simulation:</label></div>
          <div class="grid9">
            <select id="select_plot1" name="select_plot1">
              <option value="0">Select One</option>
              <?php
              foreach ($plots1 as $plot)
              {
                echo '<option value="' . $plot->PlotID . '">Version ' . $plot->PlotVersion . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>
      </div>
      
      <div id="simulation2">
        <div class="formRow" id="exist_sim2">
          <div class="grid3"><label>Plot from Second Configuration:</label></div>
          <div class="grid9">
            <select id="select_plot2" name="select_plot2">
              <option value="0">Select One</option>
              <?php
              foreach ($plots2 as $plot)
              {
                echo '<option value="' . $plot->PlotID . '">Version ' . $plot->PlotVersion . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>
      </div>
      
      <div class="formRow">
        <input type="submit" value="Choose Simulations" class="buttonM bGreen mb10 mt5" >
      </div>

    </div>
  </fieldset>
</form>
