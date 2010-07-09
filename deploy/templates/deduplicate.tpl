{if isset($new_name) and !isset($error)}
    <h1>Everything has worked out!</h1>
    <p>
      Thank you for helping to resolve this problem. You may now <a href="/">login</a> with
	{if empty($new_name)}
      your username and password unchanged.
	{else}
		your new username. Your password has remained unchanged.
	{/if}
    </p>
{else}
    <h1>Username Duplication Error!</h1>
	{if isset($error)}
    <div class="error">{$error}</div>
		{if $error eq 'The name you have chosen is unacceptable.'}
    <p style="text-align: center;">Your name must follow these rules:</p>
    <ul style="padding:20px;width: 500px;margin-left: auto;margin-right: auto;border: 1px solid red;">
      <li>8 - 24 characters long</li>
      <li>Start with a letter</li>
      <li>Contain only letters, numbers, underscores, and hypens</li>
      <li>Contain no more than one underscore or hypen in a row</li>
    </ul>
		{/if}
	{/if}
    <p>
      You have reached this page because your username is currently being used by more than 1 player. This is a <em>problem</em> that must <em>remedied</em>.
    </p>
	{if $locked}
    <h6>You are too late to save your name. This username has already been reserved by another player.</h6>
    <p>
      Somebody beat you to the punch, and your username has been reserved by another player. You must change your name. Your character will remain intact, but you <em>must</em> change your name.
    </p>
    <form action="" method="post">
      <p>
        <input type="text" name="new_name" value=""> <input type="submit" value="Change My Name">
      </p>
    </form>
	{else}
    <ul>
      <li><strong>First</strong>, know that your username is being used by precisely {$count-1} other player{if $count gt 2}s{/if}.</li>
      <li><strong>Next</strong>, know that you are the <strong>{if $age eq 1}1st{elseif $age eq 2}2nd{else}3rd{/if}</strong> player in line for this name.</li>
      <li><strong>Lastly</strong>, know that you may lock this username for yourself or change it. If you change it, you are freeing this username up for someone else to use. If you lock it, you have reserved it for yourself and no one else will be able to use it.</li> 
    </ul>
    <form action="" method="post">
      <p>
        <input type="text" name="new_name" value=""> <input type="submit" value="Change My Name">
      </p>
      <span>or</span>
      <p>
        <input type="checkbox" name="lock" value="1"> <input type="submit" value="Keep My Name and Lock It">
      </p>
    </form>
	{/if}
{/if}
