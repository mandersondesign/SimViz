<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script type="text/javascript">
  $(document).ready(function()
  {
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
  
</script>


<div class="wrapper">
  <div class="fluid">
    <div class="grid12">

      <a href="<?php echo base_url(); ?>index.php/manager/add_plot/<?php echo $simID;?>" class="buttonM bGreen mb10 mt5" style="margin-top: 10px;">Add New Plot</a>

      <div class="widget">
        
        
        <div class="whead"><h6>Plots</h6><div class="clear"></div></div>
        <div id="dyna" class="hiddenpars">
          <a class="tOptions" title="Options"><img src="<?php echo base_url(); ?>include/images/icons/options" alt="" /></a>
          <table cellpadding="0" cellspacing="0" border="0" class="vTable" id="dynamicVTable">
            <thead>
              <tr>
                <th>Plots<span class="sorting" style="display: block;"></span></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($plots as $plot)
              {
                ?>
                <tr class="gradeX">
                  <td><a class="LoadingDialog_open" href="<?php echo site_url(); ?>/simviz/plot/<?php echo $plot->PlotID; ?>/">Version <?php echo $plot->PlotVersion; ?></a></td>
                </tr>
                <?php
              }
              ?>
            </tbody>
          </table> 
        </div>
        <div class="clear"></div> 
      </div>
        <br><a href="<?php echo base_url();?>index.php/manager/add_plot/<?php echo $simID;?>" class="buttonS bDefault">Add New Plot</a>
    </div>
  </div>

</div>


<script>
  $(document).ready(function() {
    //Add variable / Variable Search Functions
    $('#LoadingDialog').dialog({
      autoOpen: false,
      width: 1000,
      height: 600,
      buttons: {}
    });

    $('.LoadingDialog_open').live("click", function (e) {
      e.preventDefault();
      $('#LoadingDialog').dialog('open');
      window.location = $(this).attr('href');
    });
  });
</script>


<div id="LoadingDialog" class="dialog" title="Loading Plot Variables">
  <h3>Loading Variables</h3>
  <div class="fluid">
    <div class="widget grid12">
      <img id="barLoader" src="<?php echo base_url(); ?>include/images/circle_loader.gif" />
    </div>
  </div>
</div>

