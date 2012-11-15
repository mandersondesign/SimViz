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

<!--<form method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/simviz/testPost">
  <input type="text" name="test"/>
  <div id="testbench">
    Testbench Name: 

    <div id="exist_testbench">
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
    <div id="new_tb">
      <br/>
      or Enter a New Name: <input type="text" name="new_testbench"/>
      </br>
    </div>
  </div>
  <br/>
  <input type="submit" value="Add New File">
</form>-->

<?php echo form_open_multipart(base_url()."index.php/simviz/addNewSimulation");?>
<!--<form method="POST" enctype="multipart/form-data" action="<?php echo base_url();?>index.php/simviz/addNewSimulation">-->
  <div id="testbench">
    Testbench Name: 

    <div id="exist_testbench">
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
    <div id="new_tb">
      <br/>
      or Enter a New Name: <input type="text" name="new_testbench"/>
      </br>
    </div>
  </div>
  <br/>
  <div id="configuration">
    Configuration Name: 

    <div id="exist_configuration">
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
    <div id="new_configuration">
      <br/>
      or Name a New Configuration: <input type="text" name="new_config"/>
      </br>
    </div>
  </div>
  <br/>
  <div id="configuration">
    Simulation Name: <input type="text" name="new_simulation"/>
  </div>

  <br/>
  Select MAT File: <input type="file" name="upfile" size="20"><br>
  <br>
  <input type="submit" value="Add New File">
</form>
