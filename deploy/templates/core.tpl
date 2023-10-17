	  <!-- Main column template section used in both splash and index -->
<section id='main-column' class='partial'>

  <!-- THE MAIN CONTENT DISPLAY SECTION -->
  {* Note the the frameBorder attribute is apparently case sensitive in some versions of ie *}
  <iframe frameBorder='0' id="main" name="main" class="main-iframe" src="{$main_src|escape}">
  </iframe>
  <noframes>
    {include file="intro.tpl"}
  </noframes>

</section> <!-- End of main-column -->
