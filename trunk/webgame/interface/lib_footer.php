<?php

// Returns the rendered footer.
function render_footer($quickstat=null, $skip_quickstat=null){
    ob_start();
    if(!$skip_quickstat){
    	$q = $quickstat;
    	if(!$q){ // If the quickstat doesn't come in from the args...
    		// Pull it from the global scope.
    		global $quickstat;
    		$q = $quickstat;
    	}
    	if(isset($q))
    	{
    	  ?>
    	  <script type='text/javascript'>
    	    refreshQuickstats('<?=$quickstat?>');
    	  </script>
    	  <?php
    	}
    }
    ?>
	</body>
	</html>
	<?php
	$res = ob_get_contents();
	ob_end_clean();
	return $res;
}

?>
