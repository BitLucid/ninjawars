{if $error eq 'log_in'}
	<span class='notice'>
		You must log in to view this section.
	</span>
{elseif $error eq 'dead'}
	<span class='ninja-notice'>You are a ghost.
	  You must resurrect before you may act again.  Go to the 
	  <a href='shrine.php'>shrine</a> for the monks to bring you back to life.
	</span>
{elseif $error eq 'frozen'}
	<span class='ninja-notice'>You are currently <span style='skyBlue'>
	  frozen</span>. You must wait to thaw before you may continue.
	</span>
{/if}
