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
	  echo "<script type='text/javascript'>
	  	parent.quickstats.location='quickstats.php?command=$quickstat';
	  	</script>\n";
	}
	?>
	</body>
	</html>
	<?php
}

?>
