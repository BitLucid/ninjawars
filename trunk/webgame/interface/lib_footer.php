<?php

function render_footer($quickstat=null){
	$q = $quickstat;
	if(!$q){ // If the quickstat doesn't come in from the args...
		// Pull it from the global scope.
		global $quickstat;
		$q = $quickstat;
	}
	if(isset($q) || isset($quickstat))
	{
	  ?>
	  <script type='text/javascript'>
	    if(window.frames["quickstats"]){
            window.frames["quickstats"].location = 'quickstats.php?command=<?=$quickstat?>';
        }
	  	</script>
	  <?php
	}
	?>
	</body>
	</html>
	<?php
}

?>
