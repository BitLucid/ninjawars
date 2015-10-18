<section id="leader-panel">
  <h2 id="leader-panel-title">
    {$clan->getName()|escape} Leader Actions
  </h2>
  <div id="leader-options" style="margin: 0 inherit 0;">
    <ul id="leader-options-list" class="clearfix">
      <li><a href="clan.php?command=invite">Recruit for your Clan</a></li>
      <li><form action="clan.php?command=disband" method="post"><input class="btn btn-warning" type="submit" value="Disband Your Clan"></form></li>
      <li><a href="clan.php?command=kick">Kick a Clan Member</a></li>
    </ul>

    <div class="glassbox" style="text-align: left;">
      <h3>Change Clan Details</h3>
      <form action="clan.php" name="avatar_and_message">
        <input type="hidden" name="command" value="update">
		<div>
		  <p>
		    <strong>Clan Name:</strong> <input id="new_clan_name" type="text" name="new_clan_name" class="textField" {literal}pattern="[A-Za-z0-9_- ]{3,24}"{/literal} required value="{$clan->getName()|escape}">
		  </p>
		  <p>
		    Clan names must be from 3 to 24 characters, and can only contain letters, numbers, spaces, underscores, or dashes, although you can request exceptions if they are fun.
		  </p>
		</div>
		<div>
		  <p>
            <strong>Clan Avatar:</strong> <input name="clan-avatar-url" type="text" value="{$clan->getAvatarUrl()|escape}">
		  </p>
		  <p>
		    To create a clan avatar, upload an image to <a href="http://www.imageshack.com" target="_blank" class="extLink">imageshack.com</a>
		    Then paste the image's full url here. Image can be .jpg or .png
		  </p>
		</div>
		<div>
		  <p>
            <strong>Clan Description:</strong> <textarea name="clan-description">{$clan->getDescription()|escape}</textarea>
	      </p>
		  <p>
            (500 character limit)
		  </p>
		</div>
        <input type="submit" value="Save Changes">
      </form>
    </div>
  </div>  <!-- End of leader-options div -->
</section>  <!-- End of leader-panel options -->
