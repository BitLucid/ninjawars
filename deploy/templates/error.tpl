{if $error eq 'log_in'}
	<h1>Become a ninja first!</h1>
    <p class='notice'>
      You must <a href='/signup'>Become a Ninja</a> or <a href="/login">log in</a> to view this section.
    </p>
{elseif $error eq 'dead'}
	<h1>You are dead</h1>
    <p class='ninja-notice death'>
      You are a ghost. You must resurrect before you may act again. Go to the <a href='/shrine' style='font-size:2em'>shrine</a> for the monks to bring you back to life, or <a href='/shrine/heal_and_resurrect' style='font-size:2em'>heal fully</a>.
    </p>
{elseif $error eq 'frozen'}
	<h1>You are frozen!</h1>
    <p class='ninja-notice'>
      You are currently <span style='skyBlue'>frozen</span>. You must wait to thaw before you may continue.
    </p>
{else}
	<h1>You can't do that</h1>
    <p class='ninja-notice'>{$error}</p>
{/if}
