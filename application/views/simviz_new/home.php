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
      
      <div class="widget">
        <div class="whead"><h6>Versions</h6><div class="clear"></div></div>
        <div id="dyna" class="hiddenpars">
          <a class="tOptions" title="Options"><img src="<?= base_url(); ?>include/images/icons/options" alt="" /></a>
          <table cellpadding="0" cellspacing="0" border="0" class="vTable" id="dynamicVTable">
            <thead>
              <tr>
                <th>Test Bench<span class="sorting" style="display: block;"></span></th>
              </tr>
            </thead>
            <tbody>
              
            </tbody>
          </table> 
        </div>
        <div class="clear"></div> 
      </div>
      
    </div>
  </div>
  
</div>

