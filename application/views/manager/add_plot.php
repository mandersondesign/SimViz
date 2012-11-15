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

<form method="POST" action="<?php echo base_url(); ?>index.php/simviz/addNewPlot">
  <div id="plot">
    Plot Name: <input type="text" name="plot_name"/>

  </div>
  <input type="hidden" name="simID" value="<?php echo $simID; ?>"/>
  <input type="submit" value="Add New File">
</form>
